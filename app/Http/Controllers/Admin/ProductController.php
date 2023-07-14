<?php

namespace App\Http\Controllers\Admin;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /* Get All Products */
    public function getAllProducts(){
        $products = Product::select('products.*', 'categories.title as category_title')
                            ->when(request("searchKey"), function($query){
                                $query->orWhere('products.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('categories.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.price', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.count', 'like', '%'.request('searchKey').'%');
                            })
                            ->leftJoin('categories', 'products.category_id', 'categories.id')
                            ->orderBy("created_at", "desc")
                            ->paginate(4);

        for($i = 0; $i < count($products); $i++){
            $products[$i]["createdAt"] = $products[$i]->created_at->diffForHumans();
            $products[$i]["updatedAt"] = $products[$i]->updated_at->diffForHumans();
        }

        return response()->json($products, 200);
    }

    /* Filter Products By Category */
    public function filterProductsByCategory($id){
        $products = Product::select('products.*', 'categories.title as category_title')
                            ->when(request("searchKey"), function($query){
                                $query->orWhere('products.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('categories.title', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.price', 'like', '%'.request('searchKey').'%')
                                      ->orWhere('products.count', 'like', '%'.request('searchKey').'%');
                            })
                            ->leftJoin('categories', 'products.category_id', 'categories.id')
                            ->where('category_id', $id)
                            ->orderBy("created_at", "desc")
                            ->paginate(4);

        for($i = 0; $i < count($products); $i++){
            $products[$i]["createdAt"] = $products[$i]->created_at->diffForHumans();
            $products[$i]["updatedAt"] = $products[$i]->updated_at->diffForHumans();
        }

        return response()->json($products, 200);
    }

    /* Create New Product */
    public function createProduct(Request $request){
        $product = $this->requestDataForProduct($request);

        $file_name = uniqid().$request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('public/products', $file_name);
        $product['image'] = $file_name;

        $newProduct = Product::create($product);
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();

        $data = $allData->where("id", $newProduct->id)->first();

        $data["createdAt"] = $data->created_at->diffForHumans();
        $data["updatedAt"] = $data->updated_at->diffForHumans();

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
    public function updateProduct(Request $request){
        $product = $this->requestDataForProduct($request);

        if($request->hasFile('image')){
            $dbData = Product::where('id', $request->id)->first();
            $dbName = $dbData->image;
            Storage::delete('public/products/'.$dbName);

            $file_name = uniqid().$request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('public/products', $file_name);
            $product["image"] = $file_name;
            // return response()->json($request, 200);
        }


        Product::where('id', $request->id)->update($product);
        $updatedData = Product::where('id', $request->id)->first();
        $allData = Product::select('products.*', 'categories.title as category_title')
                        ->leftJoin('categories', 'products.category_id', 'categories.id')
                        ->get();

        $data = $allData->where("id", $updatedData->id)->first();

        $data["createdAt"] = $data->created_at->diffForHumans();
        $data["updatedAt"] = $data->updated_at->diffForHumans();

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
