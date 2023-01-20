<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /* Get All Products */
    public function getAllProducts(){
        $products = Product::select('products.*', 'categories.title as category_title')
                            ->leftJoin('categories', 'products.category_id', 'categories.id')
                            ->get();

        for($i = 0; $i < count($products); $i++){
            $products[$i]["createdAt"] = $products[$i]->created_at->format('M d, Y - h:s A');
            $products[$i]["updatedAt"] = $products[$i]->updated_at->format('M d, Y - h:s A');
        }

        return response()->json($products, 200);
    }

    /* Create New Product */
    public function createProduct(Request $request){
        $product = $this->requestDataForProduct($request);

        $file_name = uniqid().$request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('public', $file_name);
        $product['image'] = $file_name;

        $newProduct = Product::create($product);
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();
        
        $data = $allData->where("id", $newProduct->id)->first();

        $data["createdAt"] = $data->created_at->format('M d, Y - h:s A');
        $data["updatedAt"] = $data->updated_at->format('M d, Y - h:s A');

        return response()->json($data, 200);
    }

    /* Delete Product */
    public function deleteProduct($id){
        $product = Product::where("id", $id)->first();
        $db_image = $product->image;
        Storage::delete('public/'.$db_image);
        Product::where('id', $id)->delete();

        return response()->json(['status' => 'delete success'], 200);
    }

    /* Get Product Data to Update */
    public function getProductDataForUpdate($id){
        $product = Product::where('id', $id)->first();
        return response()->json($product, 200);
    }

    /* Update Product */
    public function updateProduct($id, Request $request){
        $product = $this->requestDataForProduct($request);

        if($request->hasFile('image')){
            $dbData = Product::where('id', $id)->first();
            $dbName = $dbData->image;
            Storage::delete('public/'.$dbName);

            $file_name = uniqid().$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public', $file_name);
            $product["image"] = $file_name;
        }

        Product::where('id', $id)->update($product);
        $updatedData = Product::where('id', $id)->first();
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();
        
        $data = $allData->where("id", $updatedData->id)->first();

        $data["createdAt"] = $data->created_at->format('M d, Y - h:s A');
        $data["updatedAt"] = $data->updated_at->format('M d, Y - h:s A');

        return response()->json($data, 200);
    }

    /* Create New Category */
    public function createCategory(Request $request){
        $newCategory = Category::create(['title' => $request->title]);
        $data = Category::where('id', $newCategory->id)->first();
        return response()->json($data, 200);
    }

    /* Get All Categories */
    public function getAllCategories(){
        $categories = Category::get();
        return response()->json($categories, 200);
    }

    /* Delete Category */
    public function deleteCategory($id){
        Category::where('id', $id)->delete();
        return response()->json([
            'status' => 'delete success'
        ], 200);
    }

    /* Update Category */
    public function updateCategory($id, Request $request){
        Category::where('id', $id)->update([
            'title' => $request->title
        ]);

        $data = Category::where('id', $id)->first();
        return response()->json($data, 200);
    }

    /* Request Data For Product Create and Update */
    private function requestDataForProduct($request){
        return [
            'title' => $request->title,
            'category_id' => $request->category,
            'price' => $request->price,
            'description' => $request->description,
        ];
    }


}
