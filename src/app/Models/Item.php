<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'condition_id',
        'name',
        'description',
        'price',
        'image',
        'brand',
    ];

    // `status_id` にデフォルト値を設定
    protected $attributes = [
        'status_id' => 1,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category', 'item_id', 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getImageUrlAttribute()
{
    // 現在の画像がある場合のみチェック
    if ($this->image) {
        $extension = pathinfo($this->image, PATHINFO_EXTENSION); // 拡張子を取得
        if (in_array($extension, ['jpeg', 'png'])) {
            return asset('storage/' . $this->image);
        }
    }

    // 画像が存在しない場合は null を返す
    return null;
}

    public function setImageAttribute($value)
    {
        if (is_file($value)) {
            $fileName = uniqid() . '_' . time() . '_' . $value->getClientOriginalName();
            $filePath = $value->storeAs('public/item_images', $fileName);
            if (!$filePath) {
                throw new \RuntimeException('Failed to store the item image.');
            }
            $this->attributes['image'] = 'item_images/' . $fileName;
        } else {
            $this->attributes['image'] = $value;
        }
    }

    public function likeCount(): int
    {
        return $this->likes()->count();
    }

    public function commentCount(): int
    {
        return $this->comments()->count();
    }

    private const STATUS_SOLD = 'Sold';
    public function isPurchasable(): bool
    {
        return $this->status && $this->status->name !== self::STATUS_SOLD;
    }
}
