<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

     /**
     *
     *  @OA\Get(
     *      path="/api/product",
     *      tags={"Product"},
     *      summary="Get all products",
     *      @OA\Response(
     *          response=200,
     *          description="Success"
     *      )
     *  )
     *
     */

    public function index()
    {
        $products = Product::all();

        return response()->json($products);
    }

    /**
     *
     *  @OA\Post(
     *      path="/api/product",
     *      tags= {"Product"},
     *      summary="Insert new product",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *              required={"name", "price"},
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="image", type="string", format="binary"),
     *                  @OA\Property(property="price", type="number", format="double"),
     *                  @OA\Property(property="discount_percentage", type="number", format="double"),
     *                  @OA\Property(property="categories", type="array",
     *                      @OA\Items( type="string", enum={"ENUM_PLACEHOLDER"} )
     *                  )
     *              )
     *          )
     *      )
     *  )
     *
     **/
    public function store(StoreProductRequest $request) // $request : object
    {
        // i recover the data validated
        $form_data = $request->validated(); //array

        // i check if it's been uploaded a file
        if($request->hasFile('image')){
            // i store the image
            $image_path = Storage::disk('public')->put('image', $request->image);
            // i create the right link
            $url = asset('storage/'.$image_path);
            // i replace the partial link that there were before
            $form_data['image'] = $url;
        }

        // i create an instance of a Product
        $product = new Product();
        // i use the fill method to avoid mass assignment
        $product->fill($form_data);
        // i save the new Product inside the DB
        $saving_success = $product->save();




        // if($request->has('categories')){
        //     $new_product->categories()->attach($request->categories);
        // }

        // i check if the key 'categories' is set
        // $test = "niente";
        if(isset($request['categories'])){
            // i create an array
            $categories_array = explode(",", $request['categories']);
            // i create a void array to be fulfilled by the saved category ids
            $categories_saved_id = [];
            // if the save was successful
            if($saving_success){
                // i cicle all the string categories
                foreach ($categories_array as $category) {
                    // i take the id searching by the name
                    $category_id = DB::table('categories')
                    ->where('name', $category)
                    ->value('id');

                    // if the id is not null
                    if($category_id != null){
                        // i attach the category id for the pivot table
                        $product->categories()->attach($category_id);
                        // i push the category id inside the array
                        $categories_saved_id[] = $category_id;
                    }
                }
            }
        }

        // i add the key categories showing the categories ids
        $product->categories_id = $categories_saved_id;
        // success message
        $product->message = "You created the new product successfully!";

        // i give back the created product
        return response()->json($product);
    }

     /**
     * @OA\Get(
     *     path="/api/product/{id}",
     *     tags={"Product"},
     *     summary="Show single product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success"
     *     )
     * )
     */

    public function show(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // $categories = DB::table('category_product')->where('product_id', $id)->pluck('category_id');
        $categories = Product::find($id)->categories()->pluck('name');

        $product->categories = $categories;

        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
