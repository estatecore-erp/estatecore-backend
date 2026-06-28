<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InquiryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property' => [
                'id' => $this->property->id,
                'title' => $this->property->title,
                'type' => $this->property->type,
                'status' => $this->property->status,
                'price' => $this->property->price,
                'location' => $this->property->location,
                'agent' => $this->property->agent ? [
                    'id' => $this->property->agent->id,
                    'name' => $this->property->agent->name,
                    'email' => $this->property->agent->email,
                ] : null,
            ],
            'client' => [
                'id' => $this->client->user->id,
                'name' => $this->client->user->name,
                'email' => $this->client->user->email,
            ],
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
