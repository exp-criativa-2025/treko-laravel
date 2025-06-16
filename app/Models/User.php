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
                // If the value is already a URL (remote avatar), return it directly
                if ($value && filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                // If the value is a local file path, create the storage URL
                if ($value && Storage::disk('public')->exists($value)) {
                    return Storage::url($value);
                }

                // If no avatar, return a default placeholder
                return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
            }
        );
    }
}
