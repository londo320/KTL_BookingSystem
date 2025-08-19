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
        ]);

        $data = ['name' => $request->name];

        if ($user->email !== $request->email) {
            $data['email'] = $request->email;
            $data['email_verified_at'] = null;
        }

        $user->update($data);

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
