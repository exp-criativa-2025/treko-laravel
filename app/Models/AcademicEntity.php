<?php

namespace App\Models;

use App\Enums\AcademicEntityType;
use Illuminate\Database\Eloquent\Model;

class AcademicEntity extends Model
{
    protected $casts = [
        'type' => AcademicEntityType::class,
    ];
}
