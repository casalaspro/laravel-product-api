<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categories = ['technology', 'videogames', 'photograph', 'furniture', 'jewels', 'edibles', 'arts', 'clothing', 'medicines', 'gadgets'];

       for($i = 0; $i< count($categories); $i++){
        $category = new Category();
        $faker = Faker::create();
        $category->name = $categories[$i];
        $category->description = ucfirst($categories[$i]) ." ". lcfirst($faker->paragraph(10));
        $category->save();
       }

       
    }
}
