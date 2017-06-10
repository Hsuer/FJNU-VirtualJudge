<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\User;
use App\Status;
use Auth;
use Hash;

class UserController extends Controller
{
	public function index() {
    	$id = Auth::user()->id;
    	$user = User::findOrFail($id);
    	return view('users.index', ['user' => $user]);
    }

    public function data() {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        $solved_list = Status::where([
                            ['user_id', '=', $id],
                            ['result', '=', 'Accepted']
                        ])
                        ->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
                        ->select('problems.origin_oj', 'problems.origin_id', 'problems.id')
                        ->distinct()
                        ->get();
        $solved_list = $solved_list->sortBy('origin_id')->groupBy('id');
        
        $unsolved_list = Status::where('user_id', '=', $id)
                        ->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
                        ->select('problems.origin_oj', 'problems.origin_id', 'problems.id')
                        ->distinct()
                        ->get();
        $unsolved_list = $unsolved_list->sortBy('origin_id')->groupBy('id');
        $unsolved_list = $unsolved_list->diffKeys($solved_list);

        $solved_list = $solved_list->flatten(1)->groupBy('origin_oj')->toArray();
        $unsolved_list = $unsolved_list->flatten(1)->groupBy('origin_oj')->toArray();

        return view('users.data', ['user' => $user, 'solved_list' => $solved_list, 'unsolved_list' => $unsolved_list]);
    }

    public function modify() {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        return view('users.modify', ['user' => $user]);
    }

    public function password() {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        return view('users.password', ['user' => $user]);
    }

    public function submission() {
        $id = Auth::user()->id;
        $user = User::findOrFail($id);
        return view('users.submission', ['user' => $user]);
    }

    public function show($id) {
    	$user = User::findOrFail($id);
    	return view('users.index', ['user' => $user]);
    }

    public function show_data($id) {
        $user = User::findOrFail($id);
        $solved_list = Status::where([
                            ['user_id', '=', $id],
                            ['result', '=', 'Accepted']
                        ])
                        ->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
                        ->select('problems.origin_oj', 'problems.origin_id', 'problems.id')
                        ->distinct()
                        ->get();
        $solved_list = $solved_list->sortBy('origin_id')->groupBy('id');
        
        $unsolved_list = Status::where('user_id', '=', $id)
                        ->leftJoin('problems', 'status.problem_id', '=', 'problems.id')
                        ->select('problems.origin_oj', 'problems.origin_id', 'problems.id')
                        ->distinct()
                        ->get();
        $unsolved_list = $unsolved_list->sortBy('origin_id')->groupBy('id');
        $unsolved_list = $unsolved_list->diffKeys($solved_list);

        $solved_list = $solved_list->flatten(1)->groupBy('origin_oj')->toArray();
        $unsolved_list = $unsolved_list->flatten(1)->groupBy('origin_oj')->toArray();

        return view('users.data', ['user' => $user, 'solved_list' => $solved_list, 'unsolved_list' => $unsolved_list]);
    }

    public function show_submission($id) {
        $user = User::findOrFail($id);
        return view('users.submission', ['user' => $user]);
    }

    public function modify_profile(Request $request) {
        $this->validate($request, [
            'nick' => 'required|min:2|max:255',
            'school' => 'min:2|max:255',
            'student_id' => 'min:2|max:255',
            'description' => 'min:2|max:255',
        ]);
        $id = $request->user()->id;
        $user = User::findOrFail($id);
        $user->nick = $request->get('nick');
        $user->school = $request->get('school');
        $user->student_id = $request->get('student_id');
        $user->description = $request->get('description');
        if ($user->save()) {
            return redirect()->back()->withInput()->withErrors(['modify_success' => 'Change profile successfully!']);
        }
    }

    public function change_password(Request $request) {
       $this->validate($request, [
            'old_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ]);
        $id = $request->user()->id;
        $user = User::findOrFail($id);
        $now_password = $user->password;
        $old_password = $request->get('old_password');
        $new_password = $request->get('password');
        if(Hash::check($old_password, $now_password)) {
            $user->password = bcrypt($new_password);
            if ($user->save()) {
                return redirect()->back()->withInput()->withErrors(['change_password_success' => 'Change password successfully!']);
            }
        }
        else {
            return redirect()->back()->withInput()->withErrors(['old_password' => 'Old password error!']);
        }
    }

    public function getUserList() {
    	return Datatables::of(User::select('id','name','nick','description', 'ac', 'solve', 'submit')->get())->make(true);
    }
}
