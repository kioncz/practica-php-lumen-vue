<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use App\Models\User;


class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action
        // acciones antes de que la solicitud llegue al controlador
        $authorization = $request->header('Authorization');

        if ($authorization) {
            [$scheme, $token] = array_pad(explode(' ', trim($authorization), 2), 2, null);

            if (strcasecmp((string) $scheme, 'Bearer') !== 0 || !$token) {
                return response()->json(['error' => 'Formato de autorización inválido'], 401);
            }
        } elseif ($request->hasCookie('token')) {
            $token = $request->cookie('token');
        } else {
            return response()->json(['error' => 'Token no proporcionado'], 401);
        }

        $secret = env('JWT_SECRET');

        if (!$secret) {
            return response()->json(['error' => 'JWT_SECRET no está configurado'], 500);
        }

        try {
            $credentials = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (ExpiredException $e) {
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token inválido'], 401);
        }

        $user = User::find($credentials->sub ?? null);

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 401);
        }

        $request->auth = $user;

        $response = $next($request);

        // Post-Middleware Action
        // acciones despues de que la solicitud llegue al controlador
        return $response;
    }
}
