<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'price' => $this->price,
            'location' => $this->location,
            'image_path' => $this->image_path,
            'agent' => new UserResource($this->whenLoaded('agent')),
            'inquiries'   => $this->whenLoaded('inquiries', function () {
                return $this->inquiries->map(fn($inquiry) => [
                    'id'         => $inquiry->id,
                    'message'    => $inquiry->message,
                    'status'     => $inquiry->status,
                    'client'     => [
                        'id'    => $inquiry->client->user->id,
                        'name'  => $inquiry->client->user->name,
                        'email' => $inquiry->client->user->email,
                    ],
                    'created_at' => $inquiry->created_at?->toDateTimeString(),
                ]);
            }),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
