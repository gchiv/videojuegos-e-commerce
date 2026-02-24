<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Post(
        path: "/api/cart",
        summary: "Agregar producto al carrito",
        tags: ["Carrito"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "product_id", type: "integer", example: 1),
                    new OA\Property(property: "quantity", type: "integer", example: 1)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Producto agregado exitosamente"),
            new OA\Response(response: 400, description: "No hay stock suficiente"),
            new OA\Response(response: 401, description: "No autorizado (Falta Token)")
        ]
    )]
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'No hay suficiente stock. Solo quedan: ' . $product->stock
            ], 400);
        }

        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $product->id)
                        ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Producto agregado a tu carrito exitosamente.']);
    }

    #[OA\Get(
        path: "/api/cart",
        summary: "Ver productos en mi carrito",
        tags: ["Carrito"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Lista de productos en el carrito")
        ]
    )]
    public function viewCart(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($cartItems);
    }
}
