<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;
use Hash;
use App\Contest;
use App\Problem;
use App\Status;
use Auth;

class ContestController extends Controller
{
    public function index() {
    	return view('contests.index');
    }

    public function getContestList() {
        $contests = Contest::leftJoin('users', 'contests.user_id', '=', 'users.id')
                        ->select('contests.id','title','begin_time','end_time', 'is_public', 'user_id', 'users.name')
                        ->orderBy('contests.id', 'desc')
                        ->get();
    	return Datatables::of($contests)->make(true);
    }

    public function create() {
        $remoteoj = config('remoteoj');
    	return view('contests.create', ['OJs' => $remoteoj]);
    }

    public function edit(Request $request, $id) {
        $user_id = $request->user()->id;
        $contest = Contest::findOrFail($id);
        if($user_id != $contest->user_id) {
            return redirect()->route('error');
        }
        $problem_set = $contest->problems()
                        ->leftJoin('problems', 'problem_id', '=', 'problems.id')
                        ->select('contests_problems.title', 'problems.title as origin_title', 'origin_oj', 'origin_id')
                        ->get();

        $remoteoj = config('remoteoj');
        return view('contests.edit', ['contest' => $contest, 'OJs' => $remoteoj, 'problem_set' => $problem_set]);
    }

    public function store(Request $request) {
    	$this->validate($request, [
    	    'title' => 'required|min:2|max:255',
    	    'begin_time' => 'required',
    	    'end_time' => 'required',
    	    'password' => 'min:6|max:255',
    	    'description' => 'min:2|max:255',
    	]);
        $begin_time = $request->get('begin_time');
        $end_time = $request->get('end_time');
        $begin_unix = strtotime($begin_time);
        $end_unix = strtotime($end_time);
        if($begin_unix - time() < 180) {
            return response()->json(['begin_time' => ["Begin time should be after 3 minutes at least"]], 422);
        }
        if($end_unix - $begin_unix < 1800) {
            return response()->json(['end_time' => ["Duration should be longer than 30 minutes"]], 422);
        }
        else if($end_unix - $begin_unix > 2592000) {
            return response()->json(['end_time' => ["Duration should be shorter than 30 days"]], 422);
        }
    	$contest = new Contest;
    	$contest->title = $request->get('title');
    	$contest->begin_time = $request->get('begin_time');
    	$contest->end_time = $request->get('end_time');
    	$contest->password = trim($request->get('password'));
    	$contest->description = $request->get('description');
    	$contest->user_id = $request->user()->id;
        if($contest->password === "") {
            $contest->is_public = 1;
        }
        else {
            // $contest->password = bcrypt($contest->password);
            $contest->is_public = 0;
        }
    	if($contest->save()) {
            $contest_id = $contest->id;
            $vids = $request->get('vids');
            $protitles = $request->get('protitles');
            for($i = 0; $i < count($vids); $i++) {
                DB::table('contests_problems')->insert(
                    ['contest_id' => $contest_id, 'problem_id' => $vids[$i], 'title' => $protitles[$i]] 
                );
            }
    		return response()->json(['id' => $contest_id]);
    	}
    }

    public function update(Request $request, $id = null) {
        $this->validate($request, [
            'title' => 'required|min:2|max:255',
            'begin_time' => 'required',
            'end_time' => 'required',
            'password' => 'min:6|max:255',
            'description' => 'min:2|max:255',
        ]);
        $begin_time = $request->get('begin_time');
        $end_time = $request->get('end_time');
        $begin_unix = strtotime($begin_time);
        $end_unix = strtotime($end_time);
        // if($begin_unix - time() < 180) {
        //     return response()->json(['begin_time' => ["Begin time should be after 3 minutes at least"]], 422);
        // }
        if($end_unix - $begin_unix < 1800) {
            return response()->json(['end_time' => ["Duration should be longer than 30 minutes"]], 422);
        }
        else if($end_unix - $begin_unix > 2592000) {
            return response()->json(['end_time' => ["Duration should be shorter than 30 days"]], 422);
        }
        $contest = Contest::findOrFail($id);
        $contest->title = $request->get('title');
        $contest->begin_time = $request->get('begin_time');
        $contest->end_time = $request->get('end_time');
        $contest->password = trim($request->get('password'));
        $contest->description = $request->get('description');
        $contest->user_id = $request->user()->id;
        if($contest->password === "") {
            $contest->is_public = 1;
        }
        else {
            // $contest->password = bcrypt($contest->password);
            $contest->is_public = 0;
        }
        if($contest->save()) {
            $contest_id = $contest->id;
            // DB::table('contests_problems')->where('contest_id', '=', $contest_id)->delete();
            $vids = $request->get('vids');
            $protitles = $request->get('protitles');
            for($i = 0; $i < count($vids); $i++) {
                $old = DB::table('contests_problems')->select('ac_num', 'submit_num')->where([['contest_id', '=', $contest_id], ['problem_id', '=', $vids[$i]]])->first();
                if(isset($old)) {
                    DB::table('contests_problems')->where([['contest_id', '=', $contest_id], ['problem_id', '=', $vids[$i]]])->delete();
                    DB::table('contests_problems')->insert(
                        ['contest_id' => $contest_id, 'problem_id' => $vids[$i], 'title' => $protitles[$i], 'ac_num' => $old->ac_num, 'submit_num' => $old->submit_num] 
                    );
                }
                else {
                    DB::table('contests_problems')->insert(
                        ['contest_id' => $contest_id, 'problem_id' => $vids[$i], 'title' => $protitles[$i]] 
                    );
                }
            }
            return response()->json(['success' => ["Create contest successfully!"]]);
        }
    }

    public function countdown($id) {
        $contest = Contest::findOrFail($id);
        return view('contests.countdown', ['contest' => $contest]);
    }

    public function show($id) {
        $contest = Contest::findOrFail($id);
        return view('contests.show', ['contest' => $contest]);
    }

    public function getProblemList($id) {
        $problem_set = Contest::findOrFail($id)->problems;
        return Datatables::of($problem_set)->make(true);
    }

    public function password($id) {
        $contest = Contest::findOrFail($id);
        return view('contests.password', ['contest' => $contest]);
    }

    public function check_password(Request $request, $id) {
        if(isset($request->user()->id)) {
            $user_id = $request->user()->id;
        }
        else {
            $user_id = null;
        }
        $contest = Contest::findOrFail($id);
        $password = $contest->password;
        $input_password = $request->get('password');
        if($input_password === $password) {
            return redirect()->route('contest.show', ['id' => $contest->id])->cookie("contest_token", bcrypt($user_id."hsuer".$contest->id), 300);
        }
        else {
            return redirect()->back()->withInput()->withErrors(['password' => 'Password error!']);
        }
    }

    public function problem($id, $pid) {
        $pid = ord($pid) - 65;
        $contest = Contest::findOrFail($id);
        $problem_set = $contest->problems->pluck('problem_id');
        $total_problems = count($problem_set);
        if($pid >= 0 && $pid < $total_problems) {
            $problem_id = $problem_set[$pid];
        }
        else {
            $problem_id = 0;
        }
        $problem_id = $problem_set[$pid];
        $problem = Problem::findOrFail($problem_id);

        $remoteoj = config('remoteoj');
        $oj_info = $remoteoj[$problem['origin_oj']];
        $is_ac = false;
        $status = null;
        if(!Auth::guest()) {
            $user_id = Auth::user()->id;
            $status = Status::where([
                ['user_id', '=', $user_id],
                ['problem_id', '=', $problem_id],
                ['contest_id', '=', $id],
            ])->take(5)->select('id', 'result', 'created_at')->orderBy('id', 'desc')->get();
            if($status->pluck('result')->contains('Accepted')) {
                $is_ac = true;
            }
        }

        return view('contests.problem', ['contest' => $contest, 'problem' => $problem, 'pid' => $pid, 'total_problems' => $total_problems, 'is_ac' => $is_ac, 'status' => $status, 'oj_info' => $oj_info]);
    }

    public function submit($id, $pid) {
        $pid = ord($pid) - 65;
        $contest = Contest::findOrFail($id);
        $problem_set = $contest->problems->pluck('problem_id');
        $total_problems = count($problem_set);
        if($pid >= 0 && $pid < $total_problems) {
            $problem_id = $problem_set[$pid];
        }
        else {
            $problem_id = 0;
        }
        $problem = Problem::findOrFail($problem_id);

        $remoteoj = config('remoteoj');
        $oj_info = $remoteoj[$problem['origin_oj']];
        $is_ac = false;
        $status = null;
        if(!Auth::guest()) {
            $user_id = Auth::user()->id;
            $status = Status::where([
                ['user_id', '=', $user_id],
                ['problem_id', '=', $problem_id],
                ['contest_id', '=', $id],
            ])->take(5)->select('id', 'result', 'created_at')->orderBy('id', 'desc')->get();
            if($status->pluck('result')->contains('Accepted')) {
                $is_ac = true;
            }
        }

        $origin_oj = $problem->origin_oj;
        $language = config('language');
        $language = $language[$origin_oj];
        return view('contests.submit', ['pid' => $pid, 'total_problems' => $total_problems, 'contest' => $contest, 'problem' => $problem, 'language' => $language, 'is_ac' => $is_ac, 'status' => $status, 'oj_info' => $oj_info]);
    }

    public function status($id) {
        $language = config('language');
        $language = collect($language);
        $contest = Contest::findOrFail($id);
        $problem_set = $contest->problems->pluck('problem_id');
        for($i = 0; $i < count($problem_set); $i++) {
            $key = $problem_set[$i];
            $contest_problem_id[$key] = chr($i + 65);
        }
        $contest_problem_id = collect($contest_problem_id);
        return view('contests.status', ['contest' => $contest, 'language' => $language, 'contest_problem_id' => $contest_problem_id]);
    }

    public function getStatusList($id) {
        $status = DB::table('status')
                    ->where('contest_id', '=', $id)
                    ->leftJoin('users', 'status.user_id', '=', 'users.id')
                    ->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
                    ->select('status.*', 'users.name', 'problems.origin_oj')
                    ->orderBy('id', 'desc')
                    ->get();
        return Datatables::of($status)->make(true);
    }

    public function standing($id) {
        $contest = Contest::findOrFail($id);
        $begin_timestamp = strtotime($contest->begin_time);
        $end_timestamp = strtotime($contest->end_time);

        $problem_set = Contest::findOrFail($id)->problems;
        for($i = 0; $i < count($problem_set); $i++) {
            $key = $problem_set[$i]->problem_id;
            $contest_problem_id[$key] = chr($i + 65);
        }

        $users_list =  Status::where('contest_id', '=', $id)
                        ->leftJoin('users', 'status.user_id', '=', 'users.id')
                        ->select('users.id', 'users.name', 'users.nick', 'users.student_id')
                        ->distinct()
                        ->get();
        for($i = 0; $i < count($users_list); $i++) {
            $users_list[$i]['ac_num'] = 0;
            $users_list[$i]['penalty'] = 0;
            for($j = 0; $j < count($problem_set); $j++) {
                $users_list[$i][chr($j + 65)] = array('ac' => 0, 'wa' => 0, 'ac_time' => 0, 'fb' => 0);
            }
        }
        $users_list = $users_list->keyBy('id');
        $users_list->all();
        $users_list = $users_list->toArray();

        $status_list = Status::where('contest_id', '=', $id)
                        ->leftJoin('users', 'status.user_id', '=', 'users.id')
                        ->select('users.id', 'status.result', 'status.problem_id', 'status.created_at')
                        ->get();
        $status_list = $status_list->toArray();

        // $first_blood[] = array();
        for($i = 0; $i < count($problem_set); $i++) {
            $key = chr($i + 65);
            $first_blood[$key] = array('fb' => 0, 'user_id' => 0, 'ac' => 0, 'submit' => 0);
        }

        for($i = 0; $i < count($status_list); $i++) {
            $problem_id = $status_list[$i]['problem_id'];
            $user_id = $status_list[$i]['id'];
            $ac_timestamp = strtotime($status_list[$i]['created_at']);
            if(!isset($contest_problem_id[$problem_id])) {
                continue;
            }
            $cpid = $contest_problem_id[$problem_id];
            $first_blood[$cpid]['submit']++;
            if(strstr($status_list[$i]['result'], 'Accept')){
                $first_blood[$cpid]['ac']++;
                if($users_list[$user_id][$cpid]['ac'] == 1) {
                    continue;
                }
                if($first_blood[$cpid]['fb'] == 0) {
                    $first_blood[$cpid]['fb'] = 1;
                    $first_blood[$cpid]['user_id'] = $user_id;
                    $users_list[$user_id][$cpid]['fb'] = 1;
                }
                $users_list[$user_id][$cpid]['ac'] = 1;
                $users_list[$user_id]['penalty'] += -1 * 20 * $users_list[$user_id][$cpid]['wa'];
                $users_list[$user_id]['penalty'] += intval(($ac_timestamp - $begin_timestamp) / 60);
                $users_list[$user_id][$cpid]['ac_time'] = $this->changeTimeType($ac_timestamp - $begin_timestamp);
                if($ac_timestamp - $begin_timestamp < 0) {
                    $users_list[$user_id][$cpid]['ac_time'] = "--:--:--";
                }
                $users_list[$user_id]['ac_num']++;
            }
            else {
                $users_list[$user_id][$cpid]['wa']--;
            }
        }
        if(!empty($users_list)) {
            foreach ($users_list as $key => $row){
                $ac_num[$key] = $row['ac_num'];
                $penalty[$key] = $row['penalty'];
            }
            array_multisort($ac_num, SORT_DESC, $penalty, SORT_ASC, $users_list);
        }
        return view('contests.standing', ['contest' => $contest, 'contest_problem_id' => $contest_problem_id, 'users_list' => $users_list, 'first_blood' => $first_blood]); 
    }

    function changeTimeType($seconds){
        if ($seconds > 3600){
            $hours = intval($seconds/3600);
            $minutes = $seconds % 3600;
            $time = $hours.":".gmstrftime('%M:%S', $minutes);
        }else{
            $time = gmstrftime('%H:%M:%S', $seconds);
        }
        return $time;
    }
} 
