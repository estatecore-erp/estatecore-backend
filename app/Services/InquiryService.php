<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Support\Facades\Auth;

class InquiryService
{
    public function store(array $data)
    {
        $data['client_id'] = Auth::id();
        return Inquiry::create($data);
    }

    public function getFilteredInquiries()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) return Inquiry::all();
        if ($user->hasRole('agent')) {
            return Inquiry::whereHas('property', fn($q) => $q->where('agent_id', $user->id))->get();
        }
        return Inquiry::where('client_id', $user->id)->get();
    }

    public function findById($id)
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) return Inquiry::findOrFail($id);
        if ($user->hasRole('agent')) {
            return Inquiry::where('id', $id)
                ->whereHas('property', fn($q) => $q->where('agent_id', $user->id))
                ->firstOrFail();
        }
        return Inquiry::where('id', $id)->where('client_id', $user->id)->firstOrFail();
    }

    public function updateStatus($id, array $data)
    {
        $inquiry = Inquiry::findOrFail($id);
        $inquiry->update($data);
        return $inquiry;
    }

    public function delete($id)
    {
        return Inquiry::destroy($id);
    }
}