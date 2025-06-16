<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',    
        'cpf',
        'role',
        'bio',
        'avatar',
        'location',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

        protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                // Se o valor no banco ($value) existir, cria a URL pública.
                // A função Storage::url() gera o caminho correto, ex: /storage/avatars/arquivo.png
                if ($value) {
                    return Storage::url($value);
                }

                // Se não houver avatar, retorne uma URL de um avatar padrão/placeholder.
                return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
            }
        );
    }
}
