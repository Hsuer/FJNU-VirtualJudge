<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Hash;
use App\Contest;

class CheckContestAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $contest_id = $request->id;
        if(isset($request->user()->id)) {
            $user_id = $request->user()->id;
        }
        else {
            $user_id = null;
        }
        $contest = Contest::select('user_id', 'is_public', 'begin_time')->findOrFail($contest_id);
        $contest = $contest->toArray();
        $contest_token = $request->cookie("contest_token");
        if($user_id === $contest['user_id']) {
            return $next($request);
        }
        if($contest['is_public'] === 1) {
            if(time() < strtotime($contest['begin_time'])) {
                return redirect()->route('contest.countdown', ['id' => $contest_id]);
            }
            return $next($request);
        }
        else if(Hash::check($user_id."hsuer".$contest_id, $contest_token)) {
            if(time() < strtotime($contest['begin_time'])) {
                return redirect()->route('contest.countdown', ['id' => $contest_id]);
            }
            return $next($request);
        }
        else {
            return redirect()->route('contest.password', ['id' => $contest_id]);
        }
    }
}
