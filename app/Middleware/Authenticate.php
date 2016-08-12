<?php namespace App\Middleware;

use Closure;

class Authenticate
{
    protected $auth;

    public function handle($request, Closure $next)
    {
        echo 'auth';

        return $next($request);
    }
}
