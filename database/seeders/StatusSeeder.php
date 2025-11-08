<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductStatus;
class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $statuses = ['Available', 'Out of Stock', 'Coming Soon'];

        foreach ($statuses as $status) {
            ProductStatus::create(['name' => $status]);
        }
    }
}
