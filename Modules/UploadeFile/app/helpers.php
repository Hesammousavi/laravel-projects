<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Modules\UploadeFile\Models\File;
use Modules\UploadeFile\Services\FileUploadService;

if( ! function_exists('upload_file') ) {
    function upload_file(
        UploadedFile $file,
        array $options,
        ?Model $relatedModel = null
    ) : File {
        return FileUploadService::make($options['disk'] , $options['directory'])
            ->upload(
                $file,
                $options['filename'] ?? null,
                $relatedModel,
                $options['replaceExists'] ?? false
            );
    }
}
