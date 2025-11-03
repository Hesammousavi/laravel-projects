<?php

namespace Modules\UploadeFile\Models\Traits;

use Modules\UploadeFile\Models\File;

trait HasFile
{
    public function files()
    {
        return $this->morphMany(File::class , 'fileable');
    }
}
