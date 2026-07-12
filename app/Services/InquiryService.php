<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;

class InquiryService
{
    // get inquiries based on role, with search/status/pagination
    public function getAll(array $filters = [])
    {
        $user = Auth::user();

        $query = Inquiry::with(['client.user', 'property.agent']);

        if ($user->role === 'agent') {
            $query->whereHas('property', fn($q) => $q->where('agent_id', $user->id));
        } elseif ($user->role === 'client') {
            $query->where('client_id', $user->client->id);
        }
        // admin: no extra scope, sees everything

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

    // get single inquiry with role check
    public function getById(int $id)
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Inquiry::with(['client.user', 'property.agent'])
                ->findOrFail($id);
        }

        if ($user->role === 'agent') {
            return Inquiry::with(['client.user', 'property.agent'])
                ->where('id', $id)
                ->whereHas('property', fn($q) => $q->where('agent_id', $user->id))
                ->firstOrFail();
        }

        return Inquiry::with(['client.user', 'property.agent'])
            ->where('id', $id)
            ->where('client_id', $user->client->id)
            ->firstOrFail();
    }

    public function store(array $data): Inquiry
    {
        $inquiry = Inquiry::create([
            'client_id'   => Auth::user()->client->id,
            'property_id' => $data['property_id'],
            'message'     => $data['message'] ?? null,
            'status'      => 'pending',
        ]);

        return $inquiry->load(['client.user', 'property.agent']);
    }

    public function update(int $id, array $data): Inquiry
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update($data);
        return $inquiry->fresh(['client.user', 'property.agent']);
    }

    public function delete(int $id): void
    {
        Inquiry::findOrFail($id)->delete();
    }
}
