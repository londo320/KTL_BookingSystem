<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Debug: Try direct database update to bypass any model issues
        $tokenRecord = \DB::table('password_reset_tokens')->where('email', $request->email)->first();
        if (!$tokenRecord || !Hash::check($request->token, $tokenRecord->token)) {
            \Log::info('Token validation failed', ['email' => $request->email, 'has_token_record' => !!$tokenRecord]);
            return back()->withInput($request->only('email'))
                        ->withErrors(['email' => 'Invalid or expired token']);
        }

        // Direct database update
        $newPasswordHash = Hash::make($request->password);
        $updated = \DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password' => $newPasswordHash,
                'remember_token' => Str::random(60),
                'updated_at' => now()
            ]);

        \Log::info('Direct database update result', [
            'email' => $request->email,
            'rows_updated' => $updated,
            'new_hash' => substr($newPasswordHash, 0, 20) . '...'
        ]);

        // Clean up the token
        \DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $status = $updated > 0 ? Password::PASSWORD_RESET : 'passwords.token';

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        
        // Debug logging to help identify the issue
        \Log::info('Password reset attempt', [
            'email' => $request->email,
            'status' => $status,
            'status_text' => __($status)
        ]);
        
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', 'Password has been reset successfully. Please login with your new password.')
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
