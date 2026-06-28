<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => new ClientResource($this->whenLoaded('client')),
            'property' => new PropertyResource($this->whenLoaded('property')),  
            'status' => $this->status,
            'message' => $this->message,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
