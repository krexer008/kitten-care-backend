<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'breed',
        'birthday',
        'gender',
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    // Связь: Кот принадлежит пользователю
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Связь: У кота много записей о кормлении
    public function feedings(): HasMany
    {
        return $this->hasMany(Feeding::class);
    }

    // Связь: У кота много записей healthRecords
    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    // Связь: У кота много записей veterinaryVisits
    public function veterinaryVisits(): HasMany
    {
        return $this->hasMany(VeterinaryVisit::class);
    }
}
