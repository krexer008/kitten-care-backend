<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CatResource extends JsonResource
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
            'name' => $this->name,
            'user_id' => $this->user_id,
            'breed' => $this->breed,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'gender' =>$this->gender,
            'created_at' =>$this->created_at->format('Y-m-d H:i:s'),
            'updated_at' =>$this->updated_at->format('Y-m-d H:i:s'),

            // Можно включить отношения, если они загружены (Eager Loading)
            'feedings' => FeedingResource::collection($this->whenLoaded('feedings')),
        ];
    }
}
