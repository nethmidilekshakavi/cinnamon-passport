<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_GROWER = 'grower';
    public const ROLE_COLLECTOR = 'collector';
    public const ROLE_BUYER = 'buyer';

    /**
     * The attributes that are mass assignable.
     * Note: 'role' is deliberately not listed, so users cannot set it themselves.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
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

        /** Sent to the frontend with the user, so the layout can show the photo. */
        protected $appends = ['photo_url'];

        protected function photoUrl(): Attribute
        {
            // A relative URL, so it works on 127.0.0.1 and on localhost alike.
            return Attribute::get(fn () => $this->photo_path ? '/storage/'.$this->photo_path : null);
        }

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

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class, 'grower_id');
    }
}
