<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        
        // Get user's accessible depots for default depot selection
        $accessibleDepots = $user->depots;
        
        return view('profile.edit', [
            'user' => $user,
            'accessibleDepots' => $accessibleDepots,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'switch_user_enabled' => ['nullable', 'boolean'],
        ]);

        $data = ['name' => $request->name];

        if ($user->email !== $request->email) {
            $data['email'] = $request->email;
            $data['email_verified_at'] = null;
        }

        // Only allow paul.carr@knowleslogistics.com to update switch_user_enabled
        if ($user->email === 'paul.carr@knowleslogistics.com') {
            $data['switch_user_enabled'] = $request->has('switch_user_enabled') ? $request->boolean('switch_user_enabled') : false;
        }

        $user->update($data);

        // Refresh the authenticated user instance to pick up the changes immediately
        // This ensures navigation updates reflect the new switch_user_enabled value
        Auth::logout();
        Auth::login($user->fresh());

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        $user->forceDelete(); // Force delete instead of soft delete

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updateDefaultDepot(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'depot_id' => ['nullable', 'exists:depots,id'],
        ]);

        // Validate user has access to selected depot
        if ($request->depot_id && !$user->depots->contains($request->depot_id)) {
            return Redirect::route('profile.edit')->withErrors(['depot_id' => 'You do not have access to this depot.']);
        }

        $user->update(['depot_id' => $request->depot_id]);

        return Redirect::route('profile.edit')->with('status', 'depot-updated');
    }
}
