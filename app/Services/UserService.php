<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function getAll(?string $type = null)
    {
        $actor = Auth::user();

        if ($actor->role === 'client') {
            throw new AuthorizationException('You do not have permission to view users.');
        }

        $query = User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })->with(['client', 'employee']);

        if ($actor->role === 'agent') {
            $query->where('role', 'client');
        } elseif ($type) {
            $query->where('role', $type === 'employee' ? 'agent' : 'client');
        }

        return $query->latest()->paginate(10);
    }

    public function getById(int $id)
    {
        return User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })
            ->with(['client', 'employee'])
            ->findOrFail($id);
    }

    public function update(int $id, array $data): User
    {
        $user = User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })->findOrFail($id);


        $user->fill([
            'name' => $data['name'],
            'phone' => $data['phone'],
        ]);
        $user->save();

        if ($user->role === 'client' && isset($data['address'])) {
            $user->client()->update(['address' => $data['address']]);
        }

        return $user->fresh(['client', 'employee']);
    }

    public function delete(int $id): void
    {
        $user = User::query()->where(function ($q) {
            $q->whereIn('role', ['client', 'agent']);
        })->findOrFail($id);

        $user->client()->delete();
        $user->employee()->delete();

        User::findOrFail($id)->delete();
    }
}
