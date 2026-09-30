<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * This is the REQUIRED seeder. The application has no registration page,
     * so this login account is the only way to get past the auth middleware.
     * Credentials: test@example.com / password
     *
     * Safe to run more than once - it updates the account instead of
     * failing on a duplicate email.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                // NOTE: no email_verified_at - the users table in this project
                // does not have that column.
                'password' => 'password',
            ]
        );

        $this->command?->newLine();
        $this->command?->info('Admin account ready: test@example.com / password');
        $this->command?->comment('Want demo content (categories, courses, teachers, students, payments, expenses)?');
        $this->command?->comment('Run: php artisan db:seed --class=DemoDataSeeder');
    }
}