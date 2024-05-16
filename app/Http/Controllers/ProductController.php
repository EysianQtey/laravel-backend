<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id','title','description','image')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image'
        ]);

        try {
            $imageName = Str::random().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('storage/product/image'), $imageName); // Move the image to public/storage/product/image
            Product::create($request->post() + ['image' => $imageName]);

            return response()->json([
                'message' => 'Post Created Successfully!!'
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while creating a post!!'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable'
        ]);

        try {
            $product->fill($request->post())->update();

            if ($request->hasFile('image')) {
                // Remove old image if exists
                if ($product->image) {
                    $oldImagePath = public_path("storage/product/image/{$product->image}");
                    if (File::exists($oldImagePath)) {
                        File::delete($oldImagePath);
                    }
                }

                $imageName = Str::random() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('storage/product/image'), $imageName); // Move the new image to public/storage/product/image
                $product->image = $imageName;
                $product->save();
            }

            return response()->json([
                'message' => 'Post Updated Successfully!!'
            ]);

        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while updating a post!!'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                $imagePath = public_path("storage/product/image/{$product->image}");
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            $product->delete();

            return response()->json([
                'message' => 'Post Deleted Successfully!!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json([
                'message' => 'Something goes wrong while deleting a post!!'
            ]);
        }
    }
}
