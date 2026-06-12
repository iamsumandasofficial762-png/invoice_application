<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtWebMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = session('jwt_token');

        if (! $token) {
            return redirect()->route('login');
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();
        } catch (\Throwable) {
            session()->forget('jwt_token');

            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        }

        if (! $user) {
            session()->forget('jwt_token');

            return redirect()->route('login');
        }

        $request->attributes->set('jwt_user', $user);
        View::share('authUser', $user);

        return $next($request);
    }
}
