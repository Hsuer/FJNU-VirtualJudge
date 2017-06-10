<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests;
use App\Problem;
use App\Status;
use Yajra\Datatables\Datatables;
use DB;
use Swoole;
use Auth;

class ProblemController extends Controller
{
	public function index(Request $request) {
		$remoteoj = config('remoteoj');
		return view('problems.index', ['OJs' => $remoteoj]);
	}

	public function getProblemList() {
		return Datatables::of(Problem::select('id','title','origin_oj','origin_id','source','father_id')->get())->make(true);
	}

	public function show($id) {
		$problem = Problem::findOrFail($id);
		$remoteoj = config('remoteoj');
		$oj_info = $remoteoj[$problem['origin_oj']];
		$branchset = Problem::where('father_id', '=', $id)
						->leftJoin('users', 'users.id', '=', 'problems.author')
						->select('problems.id', 'problems.title', 'problems.updated_at', 'users.name')
						->get();
		$is_ac = false;
		$status = null;
		if(!Auth::guest()) {
			$user_id = Auth::user()->id;
			$status = Status::where([
				['user_id', '=', $user_id],
				['problem_id', '=', $id],
			])->take(5)->select('id', 'result', 'created_at')->orderBy('id', 'desc')->get();
			if($status->pluck('result')->contains('Accepted')) {
				$is_ac = true;
			}
		}
		return view('problems.show', ['problem' => $problem, 'branchset' => $branchset, 'is_ac' => $is_ac, 'status' => $status, 'oj_info' => $oj_info]);
	}

	public function origin($id) {
		$problem = Problem::findOrFail($id);
		$remoteoj = config('remoteoj');
		$oj_info = $remoteoj[$problem['origin_oj']];
		header("Location: ".$oj_info['pid'].$problem->origin_id); 
		exit;
	}

	public function submit($id) {
		$problem = Problem::findOrFail($id);
		$origin_oj = $problem->origin_oj;
		$language = config('language');
		if(isset($language[$origin_oj])) {
			$language = $language[$origin_oj];
		}
		else {
			$language = null;
		}
		return view('problems.submit', ['problem' => $problem, 'language' => $language]);
	}

	public function rewrite($id) {
		$problem = Problem::findOrFail($id);
		return view('problems.rewrite', ['problem' => $problem]);
	}

	public function restore(Request $request, $id) {
		$this->validate($request, [
	        'title' => 'required|min:2|max:64',
	        'description' => 'required|min:10',
	    ]);
		$problem = Problem::findOrFail($id);
		if($problem-> father_id != null) {
			$problem->title = trim($request->get('title'));
			$problem->description = trim($request->get('description'));
			$problem->input = trim($request->get('input'));
			$problem->output = trim($request->get('output'));
			$problem->hint = trim($request->get('hint'));
			if (!$problem->save()) {
				return redirect()->back()->withInput()->withErrors('Submit error!');
			}
			else {
				return redirect()->route('problem.show', ['id' => $problem->id]);
			}
		}
		else {
			$new_problem = new Problem; 
			$new_problem->origin_oj = $problem->origin_oj;
			$new_problem->origin_id = $problem->origin_id;
			$new_problem->time = $problem->time;
			$new_problem->memory = $problem->memory;
			$new_problem->special_judge = $problem->special_judge;
			$new_problem->sample_input = $problem->sample_input;
			$new_problem->sample_output = $problem->sample_output;
			$new_problem->ac_num = 0;
			$new_problem->submit_num = 0;
			$new_problem->available = 0;
			$new_problem->father_id = $id;
			$new_problem->title = trim($request->get('title'));
			$new_problem->description = trim($request->get('description'));
			$new_problem->input = trim($request->get('input'));
			$new_problem->output = trim($request->get('output'));
			$new_problem->hint = trim($request->get('hint'));
			$new_problem->author = $request->user()->id;
			$new_problem->source = "Rewrite by local@".$request->user()->name;
			if (!$new_problem->save()) {
				return redirect()->back()->withInput()->withErrors('Submit error!');
			}
			else {
				return redirect()->route('problem.show', ['id' => $new_problem->id]);
			}
		}
	}

	public function processString($str) {
		$_str = preg_replace("/[^a-zA-Z0-9]/","", $str);
		$reg = '/([a-zA-Z]+)(\d+)/';
		if(preg_match($reg, $_str, $result)) {
			$ret['origin_oj'] = strtoupper($result[1]);
			$ret['origin_id'] = $result[2];
			return $ret;
		}
		return null;
	}

	public function query($str) {
		$ret = $this->processString($str);
		if(!isset($ret)) return;
		$origin_oj = $ret['origin_oj'];
		$origin_id = $ret['origin_id'];
		$arr = Problem::where('origin_oj', $origin_oj)->where('origin_id', $origin_id)->select('id', 'updated_at', 'title')->first();
		if(isset($arr)) {
			return response()->json([
				"status" => 1,
				"oj" => $origin_oj,
				"id" => $origin_id,
				"vid" => $arr['id'],
				"title" => $arr['title'],
				"time" => $arr['updated_at'],
			]);
		}
	}

	public function crawl($str) {
		$ret = $this->processString($str);
		if(isset($ret)) {
			$oj = $ret['origin_oj'];
			$id = $ret['origin_id'];
			$is_added = DB::table('problems')->where('origin_oj', $oj)->where('origin_id', $id)->count();
			if($is_added) {
				return response()->json([
					"status" => 0,
					"msg" => "This problem already exists.",
				]);
			}
			else {
				$key = $oj."-".$id;
				if (Cache::has($key)) {
				    return response()->json([
						"status" => 0,
						"msg" => "System is crawling now.",
					]);
				}
				else {
					Cache::add($key, 'true', 3);	//minutes
					$arr['task'] = "crawl";
					$arr['oj'] = $oj;
					$arr['id'] = $id;
					$str = json_encode($arr);
					$this->client($str);
					return response()->json([
						"status" => 1,
						"msg" => "Please waiting for crawling.",
					]);
				}
			}
		}
		else {
			return response()->json([
				"status" => 0,
				"msg" => "Illegal input.",
			]);
		}
	}

	public function recrawl($str) {
		$ret = $this->processString($str);
		if(isset($ret)) {
			$arr['oj'] = $ret['origin_oj'];
			$arr['id'] = $ret['origin_id'];
			$arr['task'] = "recrawl";
			$str = json_encode($arr);
			$this->client($str);
			return response()->json([
				"msg" => "Please waiting for crawling.",
			]);
		}
	}

	public function client($str) {
		$client = new Swoole\Client(SWOOLE_SOCK_TCP | SWOOLE_KEEP);
		if(@!$client->connect('0.0.0.0', 9503)) {
			exit();
		}
		else {
			$client->send($str);
		}
	}
}
