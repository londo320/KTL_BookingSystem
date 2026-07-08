<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register', [
            'depots' => Depot::orderBy('name')->get(),
            'customers' => Customer::orderBy('name')->get(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'requested_account_type' => ['required', 'in:knowles,customer'],
            'requested_depot_ids' => ['required', 'array', 'min:1'],
            'requested_depot_ids.*' => ['integer', 'exists:depots,id'],
            'requested_customer_ids' => [
                Rule::requiredIf(fn () => $request->input('requested_account_type') === 'customer'),
                'array',
            ],
            'requested_customer_ids.*' => ['integer', 'exists:customers,id'],
        ]);

        // Check if a soft-deleted user exists with this email
        $existingSoftDeletedUser = User::withTrashed()->where('email', $request->email)->first();
        if ($existingSoftDeletedUser && $existingSoftDeletedUser->trashed()) {
            return back()->withErrors([
                'email' => 'This email address was previously registered but the account has been deactivated. Please contact your system administrator to restore your access.'
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'requested_account_type' => $request->requested_account_type,
            'requested_depot_ids' => $request->input('requested_depot_ids', []),
            'requested_customer_ids' => $request->input('requested_customer_ids', []),
        ]);

        event(new Registered($user));
        
        // Send admin notification for approval
        AdminNotificationService::sendNewUserRegistration($user);

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
