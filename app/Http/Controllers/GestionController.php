<?php

namespace App\Http\Controllers;

use App\Models\Tickets;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->auth->role !== 'admin') {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json(
            Tickets::with('user:id,name')->latest()->get()
        );
    }
}