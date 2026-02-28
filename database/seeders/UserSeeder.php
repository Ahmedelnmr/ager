<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'المالك',
                'email'    => 'owner@rental.com',
                'phone'    => '0500000001',
                'password' => Hash::make('password'),
                'role'     => 'owner',
            ],
            [
                'name'     => 'المشرف',
                'email'    => 'admin@rental.com',
                'phone'    => '0500000002',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'المحاسب',
                'email'    => 'accountant@rental.com',
                'phone'    => '0500000003',
                'password' => Hash::make('password'),
                'role'     => 'accountant',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $user = User::updateOrCreate(['email' => $userData['email']], $userData);
            $user->syncRoles([$role]);
        }
    }
}
