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
        'name',
        'description',
        'price',
        'condition',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
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
        return $this->hasMany(Like::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/default-item.png');
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
