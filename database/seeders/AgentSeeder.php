<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'     => 'John Agent',
            'email'    => 'agent@estatecore.com',
            'password' => 'agent123456',
            'phone'    => '0771234567',
            'role'     => 'agent',
        ]);

        Employee::create([
            'user_id'   => $user->id,
            'hire_date' => today(),
        ]);
    }
}
