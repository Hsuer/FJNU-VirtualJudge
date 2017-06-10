<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use App\Status;
use App\Solution;
use Swoole;

class StatusController extends Controller
{
    public function index() {
    	$language = config('language');
    	$language = collect($language);
    	$remoteoj = config('remoteoj');
    	return view('status.index', ['language' => $language, 'OJs' => $remoteoj]);
    }

    public function getStatus($id) {
    	$status = Status::where('id', '=', $id)->select('result', 'time', 'memory')->get();
    	$status = $status->toArray();
    	return response()->json([
    		"id" => $id,
    		"result" => $status[0]['result'],
    		"time" => $status[0]['time'],
    		"memory" => $status[0]['memory']
    	]);
    }

	public function getStatusList() {
		$status = Status::where('contest_id', '=', NULL)
					->leftJoin('users', 'status.user_id', '=', 'users.id')
					->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
					->select('status.*', 'users.name', 'problems.origin_oj', 'problems.origin_id')
					->orderBy('id', 'desc')
					->get();
		return Datatables::of($status)->make(true);
	}

	public function show($id) {
		$status = Status::where('status.id', '=', $id)
					->leftJoin('users', 'status.user_id', '=', 'users.id')
					->leftJoin('solutions', 'status.id', '=', 'solutions.id')
					->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
					->leftJoin('compile_info', 'status.id', '=', 'compile_info.status_id')
					->select('status.*', 'users.name', 'solutions.code', 'problems.origin_oj', 'problems.origin_id', 'compile_info.info as ceinfo')
					->first();
		return view('status.show', ['status' => $status]);
	}

	public function store(Request $request) {
		$this->validate($request, [
	        'language' => 'required',
	        'code' => 'required|min:10',
	    ]);
	    $status = new Status;
	    $status->user_id = $request->user()->id;
	    $status->problem_id = $request->get('problem_id');
	    $status->contest_id = $request->get('contest_id');
	    $status->language = $request->get('language');
	    $status->result = 'Pending';
	    $status->time = 0;
	    $status->memory = 0;
	    $status->length = strlen($request->get('code'));
	    $is_share = $request->get('share');
	    if($is_share == 1) {
	    	$status->is_public = 1;
	    }
	    else {
	    	$status->is_public = 0;
	    }
	    if ($status->save()) {
	    	$solution = new Solution;
	    	$solution->code = $request->get('code');
	    	$solution->save();
	    	$status_id = $status->id;
	    	$this->client($status_id, $status->contest_id);
	    	if($status->contest_id == NULL) {
	    		return redirect('status');
	    	}
	        else {
	        	return redirect()->route('contest.status', ['id' => $status->contest_id]);
	        }
	    } else {
	        return redirect()->back()->withInput()->withErrors('Submit error!');
	    }
	}

	public function rejudge($id) {
		$status = Status::findOrFail($id);
		$status->updated_at = date('Y-m-d H:i:s', time());
		$status->result = "Rejudging";
		if($status->save()) {
			$this->client($id, $status->contest_id);
			return response()->json('success');
		}
	}

	public function client($status_id, $contest_id) {
		$arr['task'] = "judge";
		$arr['status_id'] = $status_id;
		$str = json_encode($arr);
		$client = new Swoole\Client(SWOOLE_SOCK_TCP);
		if(@!$client->connect('0.0.0.0', 9503)) {
			if($contest_id != null) {
				header('Location: '. route('contest.status', ['id' => $contest_id]));
			}
			else {
				header('Location: '. route('status'));
			}
			exit();
		}
		else {
			$client->send($str);
		}
	}
}
