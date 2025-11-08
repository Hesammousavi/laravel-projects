<?php

namespace Modules\UploadeFile\Enums;

enum DiskType : string  {
    case PUBLIC = 'public';
    case PRIVATE = 'local';
    case S3_PUBLIC = 's3-public';
    case S3_PRIVATE = 's3-private';
}
