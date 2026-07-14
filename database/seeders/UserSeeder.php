<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Andi Wijaya', 'email' => 'andi@example.com', 'phone' => '0812-3456-7890'],
            ['name' => 'Siti Rahayu', 'email' => 'siti@example.com', 'phone' => '0813-9876-5432'],
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'phone' => '0857-1122-3344'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'phone' => '0822-5566-7788'],
            ['name' => 'Rudi Hartono', 'email' => 'rudi@example.com', 'phone' => '0811-2233-4455'],
            ['name' => 'Maya Sari', 'email' => 'maya@example.com', 'phone' => '0856-7788-9900'],
            ['name' => 'Joko Susilo', 'email' => 'joko@example.com', 'phone' => '0812-0000-0001'],
            ['name' => 'Ani Wulandari', 'email' => 'ani@example.com', 'phone' => '0812-0000-0002'],
            ['name' => 'Hendra Kusuma', 'email' => 'hendra@example.com', 'phone' => '0812-6677-8899'],
            ['name' => 'Fitri Anggraini', 'email' => 'fitri@example.com', 'phone' => '0838-1234-5678'],
            ['name' => 'Agus Prasetyo', 'email' => 'agus@example.com', 'phone' => '0812-0000-0003'],
            ['name' => 'Rina Melati', 'email' => 'rina@example.com', 'phone' => '0812-0000-0004'],
            ['name' => 'Bambang Wijaya', 'email' => 'bambang@example.com', 'phone' => '0819-5544-3322'],
            ['name' => 'Sri Mulyani', 'email' => 'sri@example.com', 'phone' => '0812-0000-0005'],
            ['name' => 'Eko Prasetya', 'email' => 'eko@example.com', 'phone' => '0812-0000-0006'],
        ];

        foreach ($customers as $customer) {
            User::query()->updateOrCreate(
                ['email' => $customer['email']],
                [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'email_verified_at' => now(),
                ],
            );
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@mantri-tani.test'],
            [
                'name' => 'Budi Santoso',
                'phone' => '0813-0000-0001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'owner@mantri-tani.test'],
            [
                'name' => 'Owner Mantri Tani',
                'phone' => '0813-0000-0002',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ],
        );
    }
}
