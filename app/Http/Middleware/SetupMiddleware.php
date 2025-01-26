<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\ConfigurationModel;
use Exception;

class SetupMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(env('APP_KEY') === null || empty(env('APP_KEY'))  && empty(config('app.key'))){
           Artisan::call('key:generate');
           Artisan::call('config:cache');
        }
        $setupStatus = setupStatus();
        if($request->is('setup/*')){
            if($setupStatus){
                return redirect()->route('home.index');
            }
            return $next($request);
        }
        if(!$setupStatus){
            return redirect()->route('setup.index');
        }
        return $next($request);
    }
}
