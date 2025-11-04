<?php

namespace Modules\UploadeFile\Services;

use Exception;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Modules\UploadeFile\Enums\DiskType;
use Modules\UploadeFile\Models\File;
use Modules\User\Models\User;

 class FileUploadService
{

    public function __construct(protected DiskType $disk) {}

    public static function make(DiskType $disk) : FileUploadService
    {
        return new self($disk);
    }

    public function upload(
        UploadedFile $file,
        string $directory,
        ?string $filename = null,
        ?Model $relatedModel = null,
        bool $replaceExists = false
    ): File {

        [$filename , $path , $extension] = $this->prepareFile($file , $directory, $filename , $replaceExists);


        return $this->storeUploadedFileInDB([
            'original_name' => pathinfo($file->getClientOriginalName() , PATHINFO_FILENAME),
            'path' => $path ,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'name' => $filename,
            'extension' => $extension,
            'disk' => $this->disk,
        ], $relatedModel);
    }

    public function uploadUserCover(
        UploadedFile $file,
        User $user
    ) {
        $date = now()->format('Y/m/d');
        $path = "/users/coveres/user-{$user->id}/$date";
        $coverName = Str::ulid();

        $files = [];

        $files['cover'] = $this->storeImageVariant($file , [
            'width' => 1200,
            'height' => 600,
            'qualtiy' => 40
        ],
        [
            'mimetype' => 'image/webp',
            'extension' => "webp"
        ],$path , $coverName , $user);

        $files['cover-thumb'] = $this->storeImageVariant($file , [
            'width' => 300,
            'height' => 150,
            'qualtiy' => 60
        ],
        [
            'mimetype' => 'image/webp',
            'extension' => "webp"
        ],$path , "$coverName-thumb" , $user);

        return $files;
    }

    protected function storeImageVariant(UploadedFile $file,array $size,array $fileType , $path , $name , $user)
    {
        $convertedImage = Image::read($file)
            ->cover($size['width'] , $size['height'])
            ->toWebp($size['qualtiy']);

        $nameWithPath = "$path/$name.{$fileType['extension']}";

        Storage::disk('public')->put($nameWithPath , (string) $convertedImage);

        $file = $this->storeUploadedFileInDB([
            'original_name' => pathinfo($file->getClientOriginalName() , PATHINFO_FILENAME),
            'path' =>  $nameWithPath,
            'mime_type' => $fileType['mimetype'],
            'size' => str()->length(  $convertedImage),
            'name' => $name,
            'extension' => $fileType['extension'],
            'disk' => $this->disk,
        ], $user );


        return $file;
    }

    protected function prepareFile(UploadedFile $file, string $directory , ?string $filename = null , bool $replaceExists)
    {
        $extension = $file->getClientOriginalExtension();

        if(empty($filename)) {
            $path = $file->store( $directory , [ 'disk' => $this->disk->value ]);
            return [basename($path) , $path , $extension];
        }

        $basename = pathinfo($filename , PATHINFO_FILENAME);
        $filename = "$basename.$extension";
        $path     = "$directory/$filename";

        if(Storage::disk($this->disk)->exists($path)) {
            if($replaceExists) {
                $this->delete($path);
            } else {
                [$filename , $path] = $this->generateUniqueFileName($directory , $basename , $extension);
            }
        }

        Storage::disk($this->disk)->putFileAs($directory , $file, $filename);

        return [$filename ,$path , $extension];
    }

    protected function generateUniqueFileName(string $directory , string $basename , string $extension)
    {
        $filename = "$basename-". Str::random(6) . ".$extension";
        $path = "$directory/$filename";

        return [$filename , $path];
    }

    protected function resolveModelBuilder(?Model $relatedModel)
    {
        if($relatedModel && ! method_exists($relatedModel , 'files'))
            throw new Exception("please add HasFile trait to " . $relatedModel::class);

        return $relatedModel ? $relatedModel->files() : File::query();
    }


    protected function storeUploadedFileInDB(array $attributes, ?Model $relatedModel)
    {
        $builder = $this->resolveModelBuilder($relatedModel);

        return $builder->create($attributes);
    }

    public function delete(string|array $paths)
    {
        File::where('disk', $this->disk)
            ->whereIn('path' , (array) $paths)
            ->each(function(File $file) {
                $file->delete();
            });

        return true;
    }
}
