<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->auth;

        if ($user->role === 'client') {
            return response()->json(
                Tickets::where('user_id', $user->id)->latest()->get()
            );
        }

        return response()->json(Tickets::latest()->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * esto muestra el formulario para crear un nuevo ticket,
     * pero en este caso no se esta utilizando
     * ya que se esta haciendo una aplicacion de tipo SPA
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:baja,media,alta',
        ]);

        $ticket = Tickets::create([
            'user_id' => $request->auth->id,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'priority' => $request->input('priority'),
            'status' => 'abierta',
        ]);

        return response()->json([
            'message' => 'Ticket creado correctamente',
            'ticket' => $ticket,
        ], 201);
    }

    /**
     * muestra un ticket especifico, pero en este caso no se esta utilizando
     * ya que se esta haciendo una aplicacion de tipo SPA
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
