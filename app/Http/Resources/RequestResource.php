<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "ruangan_id" => $this->ruangan_id,
            "title" => $this->title,
            "description" => $this->description,
            "date" => $this->date,
            "start" => $this->start,
            "end" => $this->end,
            "status" => $this->status
        ];
    }
}
