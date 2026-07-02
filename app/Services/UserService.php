<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAll(?string $type = null)
    {
        $query = User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })->with(['client', 'employee']);

        if ($type) {
            $query->where('role', $type === 'employee' ? 'agent' : 'client');
        }

        return $query->latest()->get();
    }

    public function getById(int $id)
    {
        return User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })
            ->with(['client', 'employee'])
            ->findOrFail($id);
    }
}
