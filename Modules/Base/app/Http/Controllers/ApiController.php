<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Modules\Base\Traits\ApiResponse;

class ApiController
{
    use AuthorizesRequests, ValidatesRequests, ApiResponse;
}
