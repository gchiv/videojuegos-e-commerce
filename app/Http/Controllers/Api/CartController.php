<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity){
            return response()->json([
                'message' => 'No hay suficiente stock, solo quedan ' . $product->stock . ' unidades disponibles.'
            ], 400);
        }
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $product->id)
            ->first();
        if ($cartItem){
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else{
            Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }
        return response()->json([
            'message' => 'Producto agregado a tu carrito exitosamente.'
        ]);
    }

    public function viewCart(Request $request){
        $cartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($cartItems);
    }
}
