<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@rsudwelasasih.id'],
            [
                'name' => 'Admin',
                'username' => 'Admin',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
            ]
        );

        User::factory()->create([
            'name' => 'Pasien Demo',
            'username' => 'pasien_demo',
            'email' => 'pasien@rsudwelasasih.id',
            'role' => 'pasien',
        ]);
    }
}
