<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class LeaseService
{
    public function getAll(array $filters = [])
    {
        $user = Auth::user();

        $query = Lease::with(['client.user', 'property.agent']);

        if ($user->role === 'agent') {
            $query->whereHas('property', fn($q) => $q->where('agent_id', $user->id));
        } elseif ($user->role === 'client') {
            $query->where('client_id', $user->client->id);
        }
        // admin: no extra scope

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('client.user', fn($qq) => $qq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('property', fn($qq) => $qq->where('title', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate(10);
    }

    public function getById(int $id)
    {
        return Lease::with(['client.user', 'property.agent'])
            ->findOrFail($id);
    }

    public function store(array $data): Lease
    {
        $lease = Lease::create([
            'client_id' => $data['client_id'],
            'property_id' => $data['property_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'monthly_rent' => $data['monthly_rent'],
            'status' => 'active',
        ]);

        Property::query()->where('id', $data['property_id'])
            ->update(['status' => 'rented']);

        return $lease->load(['client.user', 'property.agent']);
    }

    public function update(int $id, array $data): Lease
    {
        $lease = Lease::findOrFail($id);
        $lease->update($data);

        if ($data['status'] === 'expired') {
            Property::query()->where('id', $lease->property_id)
                ->update(['status' => 'available']);
        }

        return $lease->fresh(['client.user', 'property.agent']);
    }

    public function delete(int $id): void
    {
        $lease = Lease::findOrFail($id);

        Property::query()->where('id', $lease->property_id)
            ->update(['status' => 'available']);

        $lease->delete();
    }
}
