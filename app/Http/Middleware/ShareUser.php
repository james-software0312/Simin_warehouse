<?php

namespace App\Http\Middleware;

use Closure;

class ShareUser
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        view()->share('user', $user);

        return $next($request);
    }
}