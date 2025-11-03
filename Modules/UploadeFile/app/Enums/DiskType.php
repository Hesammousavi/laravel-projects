<?php

namespace Modules\UploadeFile\Enums;

enum DiskType : string  {
    case PUBLIC = 'public';
    case PRIVATE = 'local';
}
