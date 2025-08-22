<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedingResource extends JsonResource
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
            'food_type' => $this->food_type,
            'weight_grams' =>$this->weight_grams,
            'date_time' =>$this->date_time->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // Включаем связанные данные, только если они были загружены (Eager Loading)
            'cat' => new CatResource($this->whenLoaded('cat')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
