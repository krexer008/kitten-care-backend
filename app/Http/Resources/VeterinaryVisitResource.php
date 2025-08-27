<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VeterinaryVisitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Здесь мы определим структуру JSON-ответа для кота
        return [
            'id' => $this->id,
            'cat_id' => $this->cat_id,
            'user_id' => $this->user_id,
            'clinic_name' => $this->clinic_name,
            'reason' => $this->reason,
            'date_time' => $this->date_time->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'next_visit_time' => $this->next_visit_time?->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Включаем связанные данные, если они были загружены
            'cat' => new CatResource($this->whenLoaded('cat')),
            'user' => new UserResource($this->whenLoaded('user')),

            // Дополнительно вычисляемые поля для удобства фронтенда
            'is_upcoming' => $this->isUpcoming(),
            'days_until_visit' => $this->daysUntilVisit(),
        ];
    }

    /**
     * Проверяет, является ли визит предстоящим
     */
    private function isUpcoming(): bool
    {
        return $this->date_time->isFuture();
    }


    /**
     * Возвращает количество дней до визита
     * Для прошедших визитов возвращает отрицательное число
     */
    private function daysUntilVisit(): int
    {
        return now() - diffInDays($this->date_time, false);
    }
}
