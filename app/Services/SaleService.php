<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class SaleService
{
    public function getAll()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Sale::with(['client.user', 'property.agent'])
                ->latest()->get();
        }

        if ($user->role === 'agent') {
            return Sale::with(['client.user', 'property.agent'])
                ->whereHas(
                    'property',
                    fn($q) =>
                    $q->where('agent_id', $user->id)
                )
                ->latest()->get();
        }

        // client sees own sales only
        return Sale::with(['client.user', 'property.agent'])
            ->where('client_id', $user->client->id)
            ->latest()->get();
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
