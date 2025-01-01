<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'category_id',
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

    public function category()
    {
        return $this->belongsTo(Category::class);
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

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image
                ? asset('storage/' . $this->image)
                : asset('images/default-item.png'),
        );
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            set: function ($value) {
                if (is_file($value)) {
                    $fileName = time() . '_' . $value->getClientOriginalName();
                    $filePath = $value->storeAs('public/item_images', $fileName);
                    if (!$filePath) {
                        throw new \Exception('Failed to store the item image.');
                    }
                    return 'item_images/' . $fileName;
                }
                return $value;
            }
        );
    }

    public function likeCount(): int
    {
        return $this->likes()->count();
    }

    public function commentCount(): int
    {
        return $this->comments()->count();
    }

    public function isPurchasable(): bool
    {
        return $this->status && $this->status->name !== 'Sold';
    }
}
