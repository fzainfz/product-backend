<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStatus;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $categories = ProductCategory::all();
        $statuses = ProductStatus::all();
        for ($i = 1; $i <= 50; $i++) {
            $product = Product::create([
                'name' => ucfirst($faker->word),
                'price' => $faker->numberBetween(10, 500),
                'product_category_id' => $categories->random()->id,
                'product_status_id' => $statuses->random()->id,
            ]);

            // Download a dummy image to a temporary file
            $imageContent = file_get_contents('https://picsum.photos/400/300');
            $tmpFile = tempnam(sys_get_temp_dir(), 'product_') . '.jpg';
            file_put_contents($tmpFile, $imageContent);

            // Attach the image using Spatie Media Library
            $product->addMedia($tmpFile)
                ->usingFileName("product_$i.jpg")
                ->toMediaCollection('products');
        }
    }
}
