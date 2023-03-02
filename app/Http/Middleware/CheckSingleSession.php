<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

use Illuminate\Support\Facades\Session;

class CheckSingleSession
{
    public function handle($request, Closure $next)
    {
        $session_id   = Session::getId();
        $last_session = Auth::user()->session_id;

        if ($session_id !== $last_session) {
            Auth::logout();

            return redirect()
            ->route('login')
            ->withErrors('Anda dikeluarkan, Terdapat akun yang sama sedang login pada perangkat lain.');
        }

        return $next($request);
    }
}
