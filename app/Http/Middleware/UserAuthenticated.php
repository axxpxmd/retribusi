<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class UserAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // if user is not user take him to his dashboard
            if (Auth::user()->pengguna->opd_id == 99999) {
                return redirect(route('home'));
            } else {
                // allow user to proceed with request
                return $next($request);
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
