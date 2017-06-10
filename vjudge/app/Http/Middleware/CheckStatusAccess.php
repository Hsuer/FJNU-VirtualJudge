<?php

namespace App\Http\Middleware;

use Closure;
use App\Status;

class CheckStatusAccess
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
        $status_id = $request->id;
        $status = Status::select('user_id', 'is_public')->findOrFail($status_id);
        if(isset($request->user()->id)) {
            $user_id = $request->user()->id;
        }
        else {
            $user_id = null;
        }
        if($status['is_public'] === 1) {
            return $next($request);
        }
        else if($user_id === $status['user_id']) {
            return $next($request);
        }
        else {
            return redirect()->route('error');
        }
    }
}
