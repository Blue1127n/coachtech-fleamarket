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
        return $this->hasMany(Transaction::class);
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    protected function profileImageUrl()
{
    if ($this->profile_image && \Storage::disk('public')->exists($this->profile_image)) {
        return asset('storage/' . $this->profile_image);
    }

    return null;
}

    protected function profileImage()
{
    return new Attribute(
        null,
        function ($value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {

                $fileName = uniqid() . '_' . time() . '_' . $value->getClientOriginalName();

                if (!in_array($value->getClientOriginalExtension(), ['jpeg', 'png'])) {
                    throw new \RuntimeException('JPEGまたはPNG形式の画像のみアップロードできます。');
                }

                $filePath = $value->storeAs('public/profile_images', $fileName);
                if (!$filePath) {
                    throw new \RuntimeException('プロフィール画像のアップロードに失敗しました。再試行してください。');
                }
                return 'profile_images/' . $fileName;
            }
            return $value;
        }
    );
}

public function purchasedItems()
{
    return $this->hasManyThrough(
        Item::class,
        Transaction::class,
        'buyer_id',
        'id',
        'id',
        'item_id'
    )->whereIn('transactions.status_id', [3, 5]);
}
}

