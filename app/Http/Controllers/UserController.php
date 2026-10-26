<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,admin',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => strtolower($request->input('email')),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $userData,
            'token' => $this->createToken($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', strtolower($request->input('email')))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        return response()->json([
            'message' => 'Inicio de sesión correcto',
            'user' => $userData,
            'token' => $this->createToken($user),
        ]);
    }

    private function createToken(User $user): string
    {
        $secret = env('JWT_SECRET');

        if (!$secret) {
            abort(500, 'JWT_SECRET no está configurado');
        }

        $now = time();

        return JWT::encode([
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $now + (int) env('JWT_TTL', 3600),
        ], $secret, 'HS256');
    }
}
