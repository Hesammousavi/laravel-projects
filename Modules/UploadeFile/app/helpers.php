<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Modules\UploadeFile\Enums\DiskType;
use Modules\UploadeFile\Models\File;
use Modules\UploadeFile\Services\FileUploadService;

if( ! function_exists('upload_file') ) {
    function upload_file(
        UploadedFile $file,
        array $options,
        ?Model $relatedModel = null
    ) : File {
        return FileUploadService::make($options['disk'])
            ->upload(
                $file,
                $options['directory'],
                $options['filename'] ?? null,
                $relatedModel,
                $options['replaceExists'] ?? false
            );
    }
}


if( ! function_exists('delete_file') ) {
    function delete_file(
        string|array $paths,
        DiskType $disk
    ) : bool {
        return FileUploadService::make($disk)->delete($paths);
    }
}
