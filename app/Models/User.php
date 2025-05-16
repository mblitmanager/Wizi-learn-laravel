<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiResource;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[ApiResource(
    paginationItemsPerPage: 10
)]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'image'
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
        ];
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function hasAdminRole(): bool
    {
        return $this->role === 'administrateur';
    }

    public function stagiaire()
    {
        return $this->hasOne(Stagiaire::class, 'user_id');
    }

    // Dans app/Models/User.php
    public function getFormattedNameAttribute()
    {
        $parts = explode(' ', $this->name);

        if (count($parts) <= 1) {
            return $this->name;
        }

        $formatted = array_shift($parts);

        foreach ($parts as $part) {
            if (!empty($part)) {
                $formatted .= ' ' . $part[0] . '.';
            }
        }

        return $formatted;
    }
}
