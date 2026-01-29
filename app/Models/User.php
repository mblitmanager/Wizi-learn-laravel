<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\ApiResource;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[ApiResource(
    paginationItemsPerPage: 10
)]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

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
        'image',
        'last_login_at',
        'last_activity_at',
        'last_login_ip',
        'is_online',
        'fcm_token',
        'last_client',
        'adresse',
        'google_access_token',
        'google_refresh_token',
        'google_token_expires_at',
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

    protected $dates = [
        'last_login_at',
        'last_activity_at'
    ];


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

    public function commercial()
    {
        return $this->hasOne(Commercial::class, 'user_id');
    }
    public function formateur()
    {
        return $this->hasOne(Formateur::class, 'user_id');
    }
    public function poleRelationClient()
    {
        return $this->hasOne(PoleRelationClient::class, 'user_id');
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

    public function parrainages()
    {
        return $this->hasMany(Parrainage::class, 'parrain_id');
    }
    public function filleuls()
    {
        return $this->hasMany(Parrainage::class, 'filleul_id');
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistories::class);
    }

    public function lastLogin()
    {
        return $this->hasOne(LoginHistories::class)->latestOfMany();
    }

    public function appUsages()
    {
        return $this->hasMany(UserAppUsage::class);
    }

    public function androidUsage()
    {
        return $this->hasOne(UserAppUsage::class)->where('platform', 'android');
    }

    public function iosUsage()
    {
        return $this->hasOne(UserAppUsage::class)->where('platform', 'ios');
    }
}
