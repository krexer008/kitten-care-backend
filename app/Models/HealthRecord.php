<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'user_id',
        'weight',
        'temperature',
        'notes',
        'record_date',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public static function find(int $id): ?HealthRecord
    {
        return HealthRecord::find($id);
    }

    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
