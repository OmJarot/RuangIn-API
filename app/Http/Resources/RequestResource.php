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
            "user" => $this->user->name,
            "ruangan" => $this->ruangan->name,
            "title" => $this->title,
            "description" => $this->description,
            "date" => $this->date,
            "start" => $this->start,
            "end" => $this->end,
            "status" => $this->status
        ];
    }
}
