<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

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

    /**
     * Get all of the preferredSources for the User
     *
     * @return HasMany
     */
    public function preferredSources()
    {
        return $this->hasMany(UserPreferredSource::class);
    }

    /**
     * Get all of the preferredAuthors for the User
     *
     * @return HasMany
     */
    public function preferredAuthors()
    {
        return $this->hasMany(UserPreferredAuthor::class);
    }

    /**
     * Get all of the preferredCategories for the User
     *
     * @return HasMany
     */
    public function preferredCategories()
    {
        return $this->hasMany(UserPreferredCategory::class);
    }
}
