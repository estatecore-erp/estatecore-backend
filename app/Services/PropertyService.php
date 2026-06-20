<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;

class PropertyService
{
    public function getAll(User $authUser)
    {
        $query = Property::with('agent')->latest();

        if ($authUser->role === 'client') {
            $query->where('status', 'available');
        }

        return $query->get();
    }

    public function getById(int $id)
    {
        return Property::with('agent')->findOrFail($id);
    }

    public function storeProperty(array $data, User $authUser)
    {
        return Property::create([
            'agent_id' => $authUser->role === 'admin'
                ? $data['agent_id']
                : $authUser->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'sale',
            'status' => 'available',
            'price' => $data['price'],
            'location' => $data['location'],
        ]);
    }

    public function updateProperty(int $id, array $data): Property
    {
        $property = Property::findOrFail($id);
        $property->update($data);
        return $property;
    }

    public function deleteProperty(int $id): void
    {
        Property::findOrFail($id)->delete();
    }
}
