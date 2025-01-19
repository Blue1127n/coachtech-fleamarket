<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'buyer_id',
        'status_id',
        'payment_method',
        'shipping_address',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function buyer()
    {
    return $this->belongsTo(User::class, 'buyer_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    private const STATUS_COMPLETED = 'COMPLETED';
    private const STATUS_CANCELLED = 'CANCELLED';

    public function isCompleted(): bool
    {
        return $this->status && $this->status->name === self::STATUS_COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status && $this->status->name === self::STATUS_CANCELLED;
    }
}
