<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        // i check if the key 'categories' is set
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
     *
     * @OA\Post(
     *     path="/api/product/{id}",
     *     tags={"Product"},
     *     summary="Update single product",
     *     description="Each fild will have a default value. If you don't change that value, we recognize that don't want to make any modify.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer")
     *     ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="string", description="It has to have 5 chars minimum", default="default", nullable=true),
     *                  @OA\Property(property="description", type="string", default="default", nullable=true),
     *                  @OA\Property(property="image", type="string", format="binary", default="default", nullable=true),
     *                  @OA\Property(property="price", type="number", format="double", default="99999999.99", nullable=true),
     *                  @OA\Property(property="discount_percentage", type="number", format="double", default="999.99", nullable=true),
     *                  @OA\Property(property="categories", type="array",
     *                      @OA\Items( type="string",  default="default", enum={"ENUM_PLACEHOLDER"} )
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *      ),
     *  )
     *
     **/
    public function update(Request $request, string $id)
    {
        // i made this variable to check if is been loaded a new image from the client
        $loaded_no_image = false;
        // i take the file choosen from the client
        $product = Product::find($id);
        // my old imiage path
        $old_image_path = $product->image;

        // i check if the Product searched is been found in the DB
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // i check if there is a $request with something inside
        if($request){
            // if i find a default value i'll set the past values from the DB
            // for the name
            if(isset($request['name']) && $request['name'] == "default"){
                $request->merge(['name' => $product->name]);
            }
            // for the description
            if(isset($request['description']) && $request['description'] == "default"){
                $request->merge(['description' => $product->description]);
            }
            // for the image
            if(isset($request['image']) && $request['image'] == null){
                // no new image detected
                $loaded_no_image = true;
                // i recreate the old image path without the asset part
                $old_image_path_short = str_replace(asset('storage/'), "", $old_image_path);
                // i check if i load the image properly
                if (Storage::disk('public')->exists($old_image_path)) {
                    // i open the file of the already saved image
                    $image_content = Storage::disk('public')->get($old_image_path);
                    // i create path to save the image in the temp dir
                    $tmpFilePath= sys_get_temp_dir() . '/' . Str::random(10) . ".jpg";
                    // i put the file in the temp dir
                    file_put_contents($tmpFilePath, $image_content);
                    // i simulate the upload of the file
                    $uploaded_image = new UploadedFile(
                        // fully qualified path to the file
                        $tmpFilePath,
                        // file name
                        "old_image.jpg",
                        // the extension
                        "image/jpg",
                        // default for upload error (UPLOAD_ERR_OK)
                        null,
                        // test-mode / Whether the test mode is active Local files are used in test mode hence the code should not enforce HTTP uploads
                        true
                    );
                    // i pass the file inside the request to pass the validation completely
                    $request->merge(['image' => $uploaded_image]);
                }

            }
            // for the price
            if(isset($request['price']) && $request['price'] == 99999999.99){
                $request->merge(['price' => $product->price]);
            }
            // for the discount_percentage
            if(isset($request['discount_percentage']) && $request['discount_percentage'] == 999.99){
                $request->merge(['discount_percentage' => $product->discount_percentage]);
            }
    
            // for the categories
            // i check if there is a value default
            if(isset($request['categories']) && explode(",", $request['categories'])[0] == "default"){
                // i set the categoies key with the "" value
                $request->merge(['categories' => ""]);
            }

            // i validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|min:5|max:255',
                'description' => 'nullable',
                'image' => 'nullable|image|max:10240', // max 10mb
                'price' => 'nullable|numeric|between:0,99999999.99',
                'discount_percentage' => 'nullable|numeric|between:0,999.99',
                'categories' => 'nullable',
            ]);
            // i catch the errors and i gire hem to the response
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()]);
            }
     
            // i retrieve the validated input
            $validated = $validator->validated();

            if($loaded_no_image){
                // i put the old link to image
                $validated['image'] = $old_image_path;  
            }else{
                // i store the image
                $image_path = Storage::disk('public')->put('image', $request->image);
                // i create the right link
                $url = asset('storage/'.$image_path);
                // i replace the partial link that there were before
                $validated['image'] = $url;  
            }
            

            $product->update($validated);

            return response()->json($product);
        
    
        }
        
        //  i recover the data validated
        //  $form_data = $request->validated(); //array

         


        //  // i check if it's been uploaded a file
        // if($request->hasFile('image')){
        //     // i store the image
        //     $image_path = Storage::disk('public')->put('image', $request->image);
        //     // i create the right link
        //     $url = asset('storage/'.$image_path);
        //     // i replace the partial link that there were before
        //     $form_data['image'] = $url;

        //     // i check if there is an old image link
        //     if($product->imgage){
        //         // i create the string to subtract
        //         $string_to_subtract = asset('storage/');
        //         // i delete the old image modifying the link
        //         Storage::disk('public')->delete(str_replace($string_to_subtract, "", $product->image));
        //     }

           
        // }

        // $product->update($form_data);


        


    }


    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
