<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\OrderList;
use App\Models\Order;
use Carbon\Carbon;

class ShopController extends Controller
{
    // Add One Item To Cart
    public function addItemsToCart(Request $request){
        $oldCartItem = Cart::where('user_id', Auth::user()->id)
                            ->where('product_id', $request->id)
                            ->first();

        if($oldCartItem){
            Cart::where('user_id', Auth::user()->id)
                ->where('product_id', $request->id)
                ->update(['quantity' => $oldCartItem->quantity+$request->quantity]);

            $data = Cart::with('product')
                        ->where('user_id', Auth::user()->id)
                        ->where('product_id', $request->id)
                        ->first();

            return response()->json(['item' => $data, 'status' => 'fails']);
        }

        Cart::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->id,
            'quantity' => $request->quantity
        ]);

        $data = Cart::with('product')
                        ->where('user_id', Auth::user()->id)
                        ->where('product_id', $request->id)
                        ->first();

        if($data){
            return response()->json(['item' => $data, 'status' => 'created']);
        }
    }

    // Get All Items In Customer's Cart
    public function getAllCartItems(){
        $data = Cart::with('product')->where('user_id', Auth::user()->id)->get();

        return response()->json(['cartItems' => $data]);
    }

    // Change Cart Item Quantity
    public function updateCartItemQuantity(Request $request){
        Cart::where('id', $request->id)->update(['quantity' => $request->quantity]);
        $cart = Cart::find($request->id);
        if($cart->quantity === $request->quantity){
            return response()->json(['message' => 'success']);
        }
        return response()->json(['message' => 'fail']);
    }

    // Delete One Cart Item
    public function deleteCartItem($id){
        Cart::find($id)->delete();
        return response()->json(['message' => 'success']);
    }

    // Order Checkout
    public function orderCheckout(Request $request){
        $cartItems = Cart::with('product')->where('user_id', Auth::user()->id)->get();

        $orderCode = $cartItems[0]->created_at->format('YmdHi').rand(10, 99);
        $totalPrice = 0;

        foreach($cartItems as $item){
            OrderList::create([
                'user_id' => Auth::user()->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'total' => $item->quantity * $item->product->price,
                'order_code' => $orderCode,
            ]);

            $totalPrice += $item->quantity * $item->product->price;
        }

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'order_code' => $orderCode,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_price' => $totalPrice,
            'status' => 0
        ]);

        if($order){
            Cart::where('user_id', Auth::user()->id)->delete();
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail']);
    }

    /**Get All Orders */
    public function getAllOrders(){
        $data = Order::with(['order_list' => function($query) {
                        $query->select('order_code', 'quantity');
                    }])
                    ->where('user_id', Auth::user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['orders' => $data]);
    }

    /** Get Order Detail */
    public function getOrderDetail($orderCode){
        $data = OrderList::with([ "product" => function($query){
                            $query->select('id', 'title', 'price');
                        }])
                        ->where('order_code', $orderCode)
                        ->get();

        return response()->json(['items' => $data]);
    }
     /** Order Item Directly */
    public function buyNow(Request $request){
        $validated = $request->validate([
            'phone' => 'required|string',
            'address' => 'required|string',
            'product_id' => 'required',
            'quantity' => 'required',
            'total' => 'required'
        ]);

        $orderCode = Carbon::now()->format('YmdHi').rand(10, 99);

        OrderList::create([
            'user_id' => Auth::user()->id,
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
            'total' => $validated['total'],
            'order_code' => $orderCode,
        ]);

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'order_code' => $orderCode,
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'total_price' => $validated['total'],
            'status' => 0
        ]);

        if($order){
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'fail']);
    }
}
