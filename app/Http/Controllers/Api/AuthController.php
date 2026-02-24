<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use OpenApi\Attributes as OA; // <--- Importamos Swagger

class AuthController extends Controller
{
    #[OA\Post(
        path: "/api/login",
        summary: "Iniciar Sesión para obtener Token",
        tags: ["Autenticación"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "email", type: "string", example: "cliente@gmail.com"),
                    new OA\Property(property: "password", type: "string", example: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Login exitoso (Devuelve Token)"),
            new OA\Response(response: 401, description: "Credenciales incorrectas")
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    #[OA\Post(
        path: "/api/logout",
        summary: "Cerrar Sesión (Requiere Token)",
        tags: ["Autenticación"],
        security: [["bearerAuth" => []]], // <--- Candadito de seguridad
        responses: [
            new OA\Response(response: 200, description: "Sesión cerrada exitosamente")
        ]
    )]
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada exitosamente']);
    }
}
