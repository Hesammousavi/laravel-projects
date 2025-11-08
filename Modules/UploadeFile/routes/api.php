<?php

use Illuminate\Support\Facades\Route;
use Modules\UploadeFile\Http\Controllers\UploadeFileController;
use Modules\UploadeFile\Http\Controllers\UploadFileController;

Route::post('/upload-chunk' , [UploadFileController::class , 'uploadChunkFile']);

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('uploadefiles', UploadeFileController::class)->names('uploadefile');
// });
