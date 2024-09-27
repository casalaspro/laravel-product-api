<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // we create an istance of Faker choosing the language method (it will not be useful for paragraphs)
        $faker = Faker::create('en_US');
        $apiKey = "zhj2Zyc6f6KZPabr2YGhQnkBbDuKQCa8PXY0t8KVFxU1S4tc7kfirMC5";
        // the products to save
        $amount = 100;

        // we make the cicle
        for($i = 0; $i < $amount; $i++){

            // we make a call to Pexels that responses with an image link
            $response = Http::withHeaders([
                // we send the apiKey inside the header of the call
                'Authorization' => $apiKey,
            ])->get('https://api.pexels.com/v1/search', [
                'query' => 'technology', // we choose the word to search inside the pictures db
                'per_page' => 1, // we want just an image
                'page' => rand(1, 1000), // we choose a random page
            ]);

            
            // if the response is successful
            if($response->successful()){
                $imageLink = $response->json('photos.0.src.medium'); // we transform the response
                $percentage_discount_possibility = $faker->numberBetween(0, 9); // we pick a number
                $product = new Product(); // we create an istance of Product
                $product->name = ucfirst($faker->word() . " " . $faker->word()); // we create a name for the Product picking two rand words
                $product->description = $faker->paragraph(10); // we create a paragraph of 10 words circa
                $product->image = "$imageLink"; // we pass the image link
                $product->price = $faker->randomFloat(2, 0, 3000); // we chose a price randomly
                
                // we add a percentage just the 30% circa of times
                if($percentage_discount_possibility >= 0 && $percentage_discount_possibility <= 3 ){
                    $product->discount_percentage = $faker->randomFloat(2, 0, 90);
                }else{
                    $product->discount_percentage = 0.00;
                }
                
                // we store the informations inside the db
                $product->save();
            }
        }
       
        
    }


}
