<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'Jane Client',
            'email'    => 'client@estatecore.com',
            'password' => 'client123456',
            'phone'    => '0779876543',
            'role'     => 'client',
        ]);

        Client::create([
            'user_id' => $user->id,
            'address' => '123 Main St, Colombo',
        ]);
    }
}
