<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    private const ARCHIVED_STATUS = 'ARCHIVED';

    public function isActive(): bool
    {
        return $this->name !== self::ARCHIVED_STATUS;
    }
}
