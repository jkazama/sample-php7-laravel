<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{

    public function handle($request, Closure $next)
    {
        // for CORS
        header('Access-Control-Allow-Origin: http://localhost:3000');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Cache-Control, accept, X-Requested-With, Expires, X-Auth-Token, Authorization');
        return $next($request);
    }
}
