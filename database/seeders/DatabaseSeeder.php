<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $adminEmail = 'a@a.com';
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'System Admin',
                'email' => $adminEmail,
                'password' => '12345',
                'role' => 'admin',
                'mfa_bypass' => true,
                'mfa_secret' => null, // Just in case column names differ, checked model: mfa_secret logic exists via accessor but column might be google2fa_secret?
                // Checking model... line 73 `protected function google2faSecret()` suggests attribute name is `google2fa_secret`?
                // Wait, `protected function google2faSecret(): \Illuminate\Database\Eloquent\Casts\Attribute`
                // This usually typically matches the method name in snake_case if it's an accessor for a column.
                // However model fillable says 'mfa_secret' (line 28).
                // Let's assume 'mfa_secret' is the column name for now based on fillable.
                'email_verified_at' => now(),
            ]);
        }
    }
}
