<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DecryptAuthenticateTokenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {

            if($request->headers->has('Authorization')) {
                $token = Crypt::decryptString(str_replace("Bearer", "", $request->header('Authorization')));
                $request->headers->set('Authorization', "Bearer {$token}");
            }

            if($request->cookies->has('x_web_token') && !$request->headers->has('Authorization')) {
                $token = Crypt::decryptString($request->cookies->get('x_web_token'));
                $request->headers->set('Authorization', "Bearer {$token}");
            }

        } catch (\Exception $e) {}

        return $next($request);
    }
}
