<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Base\Traits\ApiResponse;
use Modules\User\Models\User;

class EnsureUserVerifiedMiddleware
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User */
        $user = Auth::user();

        if(!$user) {
            return $next($request);
        }

        $unverifiedContacts = $user->unverifiedContacts();

        if(! $unverifiedContacts->isEmpty()) {
            return $this->errorResponse(
            __('auth::verification.please_verify_contact_before_proceeding') ,
            [
                'unverified_contacts' => $unverifiedContacts,
                'redirect_to' => 'verification',
                'requires_verification' => true
            ] , code : Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
