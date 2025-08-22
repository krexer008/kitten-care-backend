<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthRecordResource extends JsonResource
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
            'weight' => $this->weight,
            'temperature' => $this->temperature,
            'notes' => $this->notes,
            'record_date' => $this->record_date->format('Y-m-d'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Включаем связанные данные, если они были загружены
            'cat' => new CatResource($this->whenLoaded('cat')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
