<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

class CustomerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer']);
    }

    public function index()
    {
        // Pull in whatever data you need—for example:
        // $bookings = auth()->user()->bookings()->latest()->get();
        // return view('customer.dashboard', compact('bookings'));

        return view('customer.dashboard');
    }
}
