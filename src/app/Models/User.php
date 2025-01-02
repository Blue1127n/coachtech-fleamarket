<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'building',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    private const DEFAULT_PROFILE_IMAGE = 'images/default-profile.png';

    protected function profileImageUrl(): Attribute
    {
        return new Attribute(
            function () {
                return $this->profile_image
                    ? asset('storage/' . $this->profile_image)
                    : asset(self::DEFAULT_PROFILE_IMAGE);
            }
        );
    }

    protected function profileImage(): Attribute
    {
        return new Attribute(
            null,
            function ($value) {
                if (is_file($value)) {
                    $fileName = time() . '_' . $value->getClientOriginalName();
                    $filePath = $value->storeAs('public/profile_images', $fileName);
                    if (!$filePath) {
                        throw new \RuntimeException('Profile image upload failed. Please try again.');
                    }
                    return 'profile_images/' . $fileName;
                }
                return $value;
            }
        );
    }
}


