<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@it-store.local')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe123!')),
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $member = User::firstOrCreate(
            ['email' => 'member@it-store.local'],
            [
                'name' => 'Member User',
                'password' => Hash::make('Member123!'),
                'email_verified_at' => now(),
            ]
        );

        if (! $member->hasRole('member')) {
            $member->assignRole('member');
        }
    }
}
