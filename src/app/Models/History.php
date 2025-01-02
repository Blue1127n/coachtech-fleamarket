<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'status_id',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getFormattedChangedAtAttribute()
    {
        return $this->changed_at ? $this->changed_at->format('Y-m-d H:i:s') : null;
    }
}
