<?php

use Illuminate\Support\Facades\Route;
use Modules\UploadeFile\Http\Controllers\UploadeFileController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('uploadefiles', UploadeFileController::class)->names('uploadefile');
});
