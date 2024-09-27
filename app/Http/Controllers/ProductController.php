<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
     *                  @OA\Property(property="price", type="number", format="float"),
     *                  @OA\Property(property="discount_percentage", type="number", format="float"),
     *              )
     *          )
     *      )
     *  )
     *
     **/
    public function store(Request $request)
    {
        $product = new Product();

        $product->fill($request->all());
        $product->save();


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
