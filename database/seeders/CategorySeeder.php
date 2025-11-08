<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Clothes', 'Electronics', 'Shoes', 'Books', 'Accessories'];

        foreach ($categories as $cat) {
            ProductCategory::create(['name' => $cat]);
        }
    }
}
