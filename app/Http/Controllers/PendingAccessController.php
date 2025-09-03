<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PendingAccessController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        return view('pending-access', [
            'user' => $user,
            'hasRoles' => $user->roles()->exists(),
            'hasDepots' => $user->depots()->exists(),
            'registeredAt' => $user->created_at
        ]);
    }
}
