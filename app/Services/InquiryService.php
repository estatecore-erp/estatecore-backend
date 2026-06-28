<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;

class InquiryService
{
    // get inquiries based on role
    public function getAll()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return Inquiry::with(['client.user', 'property.agent'])
                ->latest()
                ->get();
        }

        if ($user->role === 'agent') {
            // only inquiries on agent's properties
            return Inquiry::with(['client.user', 'property.agent'])
                ->whereHas(
                    'property',
                    fn($q) =>
                    $q->where('agent_id', $user->id)
                )
                ->latest()
                ->get();
        }

        // client sees own inquiries only
        return Inquiry::with(['client.user', 'property.agent'])
            ->where('client_id', $user->client->id)
            ->latest()
            ->get();
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
                ->whereHas(
                    'property',
                    fn($q) =>
                    $q->where('agent_id', $user->id)
                )
                ->firstOrFail();
        }

        // client can only view own inquiry
        return Inquiry::with(['client.user', 'property.agent'])
            ->where('id', $id)
            ->where('client_id', $user->client->id)
            ->firstOrFail();
    }

    // client submits inquiry on a property
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

    // agent/admin updates inquiry status
    public function update(int $id, array $data): Inquiry
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update($data);
        return $inquiry->fresh(['client.user', 'property.agent']);
    }

    // admin deletes inquiry
    public function delete(int $id): void
    {
        Inquiry::findOrFail($id)->delete();
    }
}
