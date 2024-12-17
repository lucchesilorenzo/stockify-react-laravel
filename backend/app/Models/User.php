<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'hashedPassword',
        'firstName',
        'lastName',
        'dateOfBirth',
        'phone',
        'bio',
        'address',
        'city',
        'zipCode',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'hashedPassword',
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
            'hashedPassword' => 'hashed',
        ];
    }

    /**
     * Get the orders for the user.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the tasks for the user.
     *
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the activities for the user.
     *
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
