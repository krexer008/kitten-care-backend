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

    protected $casts = [
        'date_time' => 'datetime',
        'next_visit_time' => 'datetime',
    ];

    public static function find(int $id)
    {
        return VeterinaryVisit::find($id);
    }

    public static function create(array $attributes)
    {
        return VeterinaryVisit::create($attributes);
    }

    public static function where(string $string, int $userId)
    {
        return VeterinaryVisit::where($string, $userId);
    }

    public function cat(): BelongsTo
    {
        return $this->belongsTo(Cat::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Проверяет, является ли визит предстоящим
     */
    public function isUpсoming(): bool
    {
        return $this->date_time->isFuture();
    }

    /**
     * Возвращает количество дней до визита
     */
    public function daysUntilVisit():int
    {
        return now() - diffInDays($this->date_time, false);
    }

    /**
     * Запрос для получения предстоящих визитов
     */
    public function scopeUpсoming($query)
    {
        return $query->where('date_time', '>', now());
    }

    /**
     * Запрос для получения прошедших визитов
     */
    public function scopePast($query)
    {
        return $query->where('date_time', '<=', now());
    }

    /**
     * Запрос для получения прошедших визитов
     */
    public function scopeForCat($query, $catId)
    {
        return $query->where('cat_id', $catId);
    }
}
