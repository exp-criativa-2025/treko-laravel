<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicEntity extends Model
{
    use HasFactory;

    protected $table = 'academic_entities';

    protected $fillable = [
        'type',
        'fantasy_name',
        'cnpj',
        'foundation_date',
        'status',
        'cep',
        'user_id'
    ];

    protected $dates = [
        'foundation_date',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}