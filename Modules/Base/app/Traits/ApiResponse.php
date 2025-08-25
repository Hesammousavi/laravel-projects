<?php

namespace Modules\Base\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function successResponse($message = null, array $data = [], $code = 200 , array $cookies = [])   : JsonResponse
    {
        $response = response()->json([
            'message' => $message,
            'data' => $data
        ], $code);

        foreach ($cookies as $value) {
            $response->withCookie($value);
        }

        return $response;
    }

    public function errorResponse($message = null,array $errors = [], $code = 400) : JsonResponse
    {
        return response()->json([
            'message' => $message,
            'errors' => $errors
        ], $code);
    }
}
