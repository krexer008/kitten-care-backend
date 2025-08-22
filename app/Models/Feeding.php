<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Feeding extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'user_id',
        'food_type',
        'weight_grams',
        'date_time',
    ];

    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
