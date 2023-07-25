<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderList;
use Carbon\Carbon;

class ItemController extends Controller
{
    // Get All Items
    public function getAllItems(){
        $data = Product::with('category')
                        ->when(request('searchKey'), function($query){
                            $query->orWhere('title', 'like', '%'.request('searchKey').'%')
                                    ->orWhere('price', 'like', '%'.request('searchKey').'%');
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json(['items' => $data]);
    }

    // Get All Categories
    public function getAllCategories(){
        $data = Category::get();

        return response()->json(['categories' => $data]);
    }

    // Filter Items By Category
    public function filterItemsByCategory($id){
        $data = Product::with('category')
                        ->where('category_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return response()->json(['items' => $data]);
    }

    // Get Latest Items
    public function getLatestItems(){
        $data = Product::orderBy('created_at', 'desc')->take(7)->get();

        return response()->json(['items' => $data]);
    }

    // Get Popular Items
    public function getPopularItems(){
        $date = Carbon::now()->subDays(7);

        $data = OrderList::where('created_at', '>=', $date)->get();

        $record = [];

        foreach($data as $order){
            if(array_key_exists($order->product_id, $record)){
                $record[$order->product_id] = $record[$order->product_id] + $order->quantity;
            }else {
                $record[$order->product_id] = $order->quantity;
            }
        }

        arsort($record);

        $product_id_array = array_keys($record);

        $items = Product::whereIn('id', $product_id_array)
                        ->orderByRaw("FIELD(id, " . implode(',', $product_id_array) . ")")
                        ->take(7)
                        ->get();

        return response()->json(['items' => $items]);
    }

    // public function getBestRatingItems(){
    //     $orders = OrderList::with('product')->where('user_id', Auth::user()->id)->get();

    //     $record = [];

    //     return response()->json(['items' => $orders]);
    // }
}
