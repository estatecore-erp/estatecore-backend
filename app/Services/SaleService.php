<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SaleService
{
    public function getAll(array $filters = [])
    {
        $user = Auth::user();
        $query = Sale::with(['client.user', 'property.agent']);

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
            $query->whereHas('property', fn($q) => $q->where('status', $filters['status']));
        }

        return $query->latest()->paginate(10);
    }

    public function getById(int $id)
    {
        return Sale::with(['client.user', 'property.agent'])
            ->findOrFail($id);
    }

    public function store(array $data): Sale
    {
        $sale = Sale::create([
            'client_id' => $data['client_id'],
            'property_id' => $data['property_id'],
            'sale_price' => $data['sale_price'],
            'sale_date' => $data['sale_date'],
        ]);

        // property permanently sold
        Property::query()->where('id', $data['property_id'])
            ->update(['status' => 'sold']);

        return $sale->load(['client.user', 'property.agent']);
    }

    public function delete(int $id): void
    {
        $sale = Sale::findOrFail($id);

        // revert property status
        Property::query()->where('id', $sale->property_id)
            ->update(['status' => 'available']);

        $sale->delete();
    }
}
