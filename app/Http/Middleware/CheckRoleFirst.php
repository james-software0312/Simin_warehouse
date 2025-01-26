<?php
// app/Http/Middleware/CheckRoleFirst.php

namespace App\Http\Middleware;

use Closure;

class CheckRoleFirst
{
    public function handle($request, Closure $next)
    {
        if ($request->session()->get('panten', false)) {
            return $next($request);
        }

        return redirect()->route('setup.license');
    }
}
