<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use Modules\UploadeFile\Services\FileUploadService;
use Modules\User\Transformers\UserResource;
use \Illuminate\Support\Str;
use Modules\UploadeFile\Enums\DiskType;

class UserController extends Controller
{
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    public function coverUpload(Request $request)
    {
        $request->validate([
            'cover' => 'required|file|max:2048|image'
        ]);


        $files = FileUploadService::make(DiskType::PUBLIC)
            ->uploadUserCover($request->file('cover') , $request->user());


        return $files;
    }
}
