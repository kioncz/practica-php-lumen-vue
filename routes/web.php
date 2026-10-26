<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Vistas públicas.
$router->get('/login', function () {
    return view('login');
});

$router->get('/register', function () {
    return view('register');
});

// Endpoints públicos de autenticación.
$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');

// Entrada principal: mostrar la vista pública de inicio de sesión.
$router->get('/', function () {
    return redirect('/login');
});

// Vista Vue para clientes. Sus datos se cargan desde GET /tickets.
$router->get('/client/tickets', function () {
    return view('client-tickets');
});

// Endpoints protegidos para tickets.
$router->group(['middleware' => 'jwt'], function () use ($router) {
    $router->get('/tickets', 'TicketController@index');
    $router->post('/tickets', 'TicketController@store');
    $router->get('/tickets/{id}', 'TicketController@show');
    $router->put('/tickets/{id}', 'TicketController@update');
    $router->delete('/tickets/{id}', 'TicketController@destroy');
});

$router->get('/{any:.*}', function () {
    return response()->json(['message' => 'Not Found.'], 404);
});
