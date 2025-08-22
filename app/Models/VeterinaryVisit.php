<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VeterinaryVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'user_id',
        'clinic_name',
        'reason',
        'notes',
        'date_time',
        'next_visit_time',
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
