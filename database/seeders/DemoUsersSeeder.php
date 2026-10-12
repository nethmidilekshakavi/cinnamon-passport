<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Demo Grower', 'email' => 'grower@example.com', 'role' => 'grower', 'phone' => '0770000001'],
            ['name' => 'Demo Collector', 'email' => 'collector@example.com', 'role' => 'collector', 'phone' => '0770000002'],
            ['name' => 'Demo Buyer', 'email' => 'buyer@example.com', 'role' => 'buyer', 'phone' => '0770000003'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrNew(['email' => $data['email']]);
            $user->forceFill($data + [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ])->save();
        }
    }
}
