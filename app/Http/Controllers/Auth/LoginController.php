<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function redirectAfterLogin()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('depot-admin')) {
            return redirect()->route('depot.dashboard');
        }

        if ($user->hasRole('site-admin')) {
            return redirect()->route('site.dashboard');
        }

        if ($user->hasRole('customer')) {
            return redirect()->route('customer.bookings.index');
        }

        return redirect('/'); // fallback
    }
}
