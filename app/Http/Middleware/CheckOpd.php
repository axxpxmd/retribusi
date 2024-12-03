<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class CheckOpd
{
    public function handle($request, Closure $next)
    {
        $opd_id = Auth::user()->pengguna->opd_id;
        $param_opd_id = (int) base64_decode($request->opd_id);
        $tahun = $request->tahun ? $request->tahun : 2024;
        $role_id = Auth::user()->pengguna->modelHasRole->role_id;

        if ($role_id == 6 || $role_id == 8 || $role_id == 9 || $role_id == 11) {
            if ($opd_id != $param_opd_id) {
                return redirect()
                    ->route('home', ['tahun' => base64_encode($tahun), 'opd_id' => base64_encode($opd_id)])
                    ->withErrors('Credential tidak cocok.');
            }
        }

        return $next($request);
    }
}
