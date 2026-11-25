<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            //user is not logged in
            abort(Response::HTTP_FORBIDDEN);
        }

        if (Auth::check()) {
            //user is logged in but not admin
            if (Auth::user()->name !== 'admin') {
                abort(Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
