<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            StatusSeeder::class,
            ProductSeeder::class,
        ]);

         User::create([
            'name' => 'Admin User',
            'email' => 'admin@demo.com',
            'password' => Hash::make('Admin@123'), // change as needed
            'is_admin' => 1,
        ]);
        $this->command->info('✅ Database seeded: categories, statuses, products, admin user');
    }
}
