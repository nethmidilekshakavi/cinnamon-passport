<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $people = [
            ['Demo Grower', 'grower@example.com', 'grower', '0770000001'],
            ['Demo Collector', 'collector@example.com', 'collector', '0770000002'],
            ['Demo Buyer', 'buyer@example.com', 'buyer', '0770000003'],
        ];

        foreach ($people as [$name, $email, $role, $phone]) {
            if (User::where('email', $email)->exists()) {
                continue;
            }

            User::forceCreate([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => $role,
                'phone' => $phone,
                'email_verified_at' => now(),
            ]);
        }
    }
}
