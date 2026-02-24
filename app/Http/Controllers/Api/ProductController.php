<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: "/api/products",
        summary: "Ver lista de videojuegos",
        tags: ["Productos"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Lista de productos recuperada exitosamente"
            )
        ]
    )]
    public function index()
    {
        // Tu código original
        $products = Product::all();
        return response()->json($products);
    }
}
