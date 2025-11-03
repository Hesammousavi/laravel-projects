<?php

namespace Modules\UploadeFile\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UploadeFile\Enums\DiskType;

// use Modules\UploadeFile\Database\Factories\FileFactory;

class File extends Model
{
    use HasFactory, HasUlids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'original_name',
        'path',
        'mime_type',
        'size',
        'name',
        'extension',
        'disk',
    ];

    protected function casts()
    {
        return [
            'disk' => DiskType::class
        ];
    }
    // protected static function newFactory(): FileFactory
    // {
    //     // return FileFactory::new();
    // }
}
