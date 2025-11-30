<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // (1) Kailangan mo ang Model ng iyong data

class APIController extends Controller
{
    /**
     * Kumuha ng listahan ng lahat ng users (READ operation).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // (2) Kunin ang lahat ng data mula sa User table
        $users = User::all();

        // (3) Ibalik ang data bilang JSON response
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }
    
    // (4) Dito mo rin ilalagay ang iba pang CRUD methods:
    // public function store(Request $request) { ... } // CREATE (Mag-save ng bagong data)
    // public function show($id) { ... }             // READ Specific (Kumuha ng isang item)
    // public function update(Request $request, $id) { ... } // UPDATE (Baguhin ang data)
    // public function destroy($id) { ... }          // DELETE (Burahin ang data)
}