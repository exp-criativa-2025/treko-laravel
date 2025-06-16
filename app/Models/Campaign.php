<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'goal',
        'start_date',
        'end_date',
        'academic_entity_id'
    ];

    // Relacionamento com AcademicEntity (se existir)
    public function academicEntity()
    {
        return $this->belongsTo(AcademicEntity::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
}
