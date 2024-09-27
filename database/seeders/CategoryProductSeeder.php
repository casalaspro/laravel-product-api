<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        // i make an instance of Faker
        $faker = Faker::create();
        // i collect all the products
        $products = Product::all();

        // i cicle al the products
        foreach ($products as $key => $product){
            // i take all categories
            $categories = Category::all();
            // i take all ids
            $categories_ids = $categories->pluck('id');
            // i take random categories
            $random_categories = $faker->randomElements($categories_ids, null);
            if(count($random_categories) != 0){
                foreach($random_categories as $category_to_attach){
                    $exists = DB::table('category_product')
                    ->where('category_id', $category_to_attach)
                    ->where('product_id', $key)
                    ->exists();
                    if(!$exists){
                        $product->categories()->attach($category_to_attach);
                        $product->save();
                    }
                    
                }
            }
        }
    }
}
