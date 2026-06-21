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
        $client->update($data);
        return $client->fresh('user');
    }

    public function deleteClient(int $id): void
    {
        Client::findOrFail($id)->delete();
    }
}
