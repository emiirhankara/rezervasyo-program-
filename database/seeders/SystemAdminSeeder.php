<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SYSTEM_ADMIN_EMAIL');
        $password = env('SYSTEM_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            throw new RuntimeException('Set SYSTEM_ADMIN_EMAIL and SYSTEM_ADMIN_PASSWORD in .env before running this seeder.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SYSTEM_ADMIN_NAME', 'ENTUR Sistem Yöneticisi'),
                'role' => 'system_admin',
                'organizer_status' => 'not_applicable',
                'password' => $password,
            ]
        );
    }
}
