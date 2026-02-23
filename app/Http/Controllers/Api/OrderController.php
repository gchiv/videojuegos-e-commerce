<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class OrderController extends Controller
{
    public function checkout(Request $request){
        return DB::transaction(function() use ($request){
            $user = $request->user();

            $cartItems = Cart::where('user_id', $user->id)->get();

            if ($cartItems->isEmpty()){
                return response()->json([
                    'message' => 'Tu carrito esta vacio'
                ],400);
            }

            $total = 0;
            foreach ($cartItems as $item){
                $product = Product::find($item->product_id);

                if($product->stock < $item->quantity){
                    throw new \Exception('No hay suficiente stock para el producto: ' . $product->name);
                }
                $total += $product->price * $item->quantity;
            }
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'completed'
            ]);

            foreach ($cartItems as $item){
                $product = Product::find($item->product_id);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price
                ]);

                $product->stock -= $item->quantity;
                $product->save();
            }

            Cart::where('user_id',$user->id)->delete();
            return response()->json([
                'message' => 'Orden creada exitosamente',
                'order_id' => $order->id,
                'total_paid' => $total
            ]);
        });
    }
}
