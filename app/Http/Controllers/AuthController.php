<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use App\Models\User;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller
{
    private $jwt_secret;
    //
    public function __construct(Request $jwt_secret)
    {
        $this-> request = $jwt_secret;
        $this->jwt_secret = env('JWT_SECRET');
    }

    public function jwt($usuario)
    {
        $payload = [
            'iss' => "lumen-jwt", // Emisor del token
            'sub' => $usuario->id, // ID del usuario
            'iat' => time(), // Tiempo de emisión
            'exp' => time() + 60*60 // Tiempo de expiración (1 hora)
        ];

        return JWT::encode($payload, $this->jwt_secret, 'HS256');
    }

    //variable $request, que es una instancia de la clase Request de Lumen,
    // que se utiliza para manejar las solicitudes HTTP entrantes.
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $usuario = Usuario::where('email', $request->input('email'))->first();

        if (!$usuario || !password_verify($request->input('password'), $usuario->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        //validacion para el login con rediccionamiento a la vista de peliculas,
        //  y creacion de cookie para el token
        $token = $this->jwt($usuario);
        $cookie = Cookie::create(
            'token',
            $token,
            time() + 3600,
            '/',
            null,
            false,
            true
        );

        return redirect('/tickets')->withCookie($cookie);

    }
    //validacion de campos vacios para el login,
    //  si los campos estan vacios se devuelve un error 400
    public function valiation_null(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!$request->input('email') || !$request->input('password')) {
            return response()->json(['error' => 'Campos vacíos'], 400);
        }
    }
}
