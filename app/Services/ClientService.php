<?php

namespace App\Services;

use App\Models\Client;

class ClientService
{
    public function getAll()
    {
        return Client::with('user')->latest()->get();
    }

    public function getById(int $id)
    {
        return Client::with('user')->findOrFail($id);
    }

    public function updateClient(int $id, array $data): Client
    {
        $client = Client::findOrFail($id);
        $user = $client->user;

        // update users table
        $user->update([
            'name' => $data['name']  ?? $user->name,
            'phone' => $data['phone'] ?? $user->phone,
        ]);

        // update clients table
        $client->update([
            'address' => $data['address'] ?? $client->address,
        ]);

        return $client->fresh('user');
    }

    public function deleteClient(int $id): void
    {
        Client::findOrFail($id)->delete();
    }
}
