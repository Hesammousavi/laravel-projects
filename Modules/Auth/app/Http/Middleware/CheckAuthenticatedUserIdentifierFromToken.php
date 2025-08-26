<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Base\Traits\ApiResponse;
use Modules\User\Models\User;

class CheckAuthenticatedUserIdentifierFromToken
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check()){
            /** @var User */
            $user = Auth::user();
            $currentToken = $user->currentAccessToken();

            if($currentToken->identifier !== $user->identifierFromRequest()){
                return $this->errorResponse(
                    message: 'unauthenticated',
                    code: 401,
                );
            }
        }

        return $next($request);
    }
}
