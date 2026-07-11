<?php

namespace App\Services;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class PropertyService
{
    public function getAll(User $authUser, array $filters = [])
    {
        $query = Property::with('agent')->latest();

        if ($authUser->role === 'client') {
            $query->where('status', 'available');
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->paginate(10);
    }

    public function getById(int $id)
    {
        return Property::with('agent')->findOrFail($id);
    }

    public function storeProperty(array $data, User $authUser)
    {
        $property = Property::create([
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

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $file = $data['image'];
            $extension = $file->getClientOriginalExtension();
            $fileName = $property->id . '.' . $extension;
            
            $destinationPath = public_path('storage/properties');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $fileName);
            $property->update(['image_path' => '/storage/properties/' . $fileName]);
        }

        return $property->load('agent');
    }

    public function updateProperty(int $id, array $data): Property
    {
        $property = Property::findOrFail($id);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $file = $data['image'];
            $extension = $file->getClientOriginalExtension();
            $fileName = $property->id . '.' . $extension;
            
            $destinationPath = public_path('storage/properties');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }
            
            $file->move($destinationPath, $fileName);
            $data['image_path'] = '/storage/properties/' . $fileName;
            unset($data['image']);
        }

        $property->update($data);
        return $property;
    }

    public function deleteProperty(int $id): void
    {
        Property::findOrFail($id)->delete();
    }
}
