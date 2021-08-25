<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AdminAuthenticated​
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // if user is not admin take him to his dashboard
            if (Auth::user()->pengguna->opd_id != 0) {
                return redirect(route('home'));
            }

            // allow admin to proceed with request
            else if (Auth::user()->pengguna->opd_id == 0) {
                return $next($request);
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
