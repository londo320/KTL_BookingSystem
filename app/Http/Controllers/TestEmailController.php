<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\CustomPasswordReset;

class TestEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'function:settings.manage']);
    }
    public function index()
    {
        $users = User::limit(5)->get();
        return view('test-email', compact('users'));
    }
    
    public function preview(Request $request)
    {
        if (!$request->has('user_id')) {
            return redirect()->route('app.test-email.index')->with('error', 'No user selected');
        }
        
        $user = User::findOrFail($request->user_id);
        $resetUrl = url('/password/reset/preview-token-123?email=' . urlencode($user->email));
        
        // Create a mock message object for the embed function
        $mockMessage = new class {
            public function embed($path) {
                return asset('images/ktl_logo_email.png');
            }
        };
        
        return view('emails.password-reset', [
            'user' => $user,
            'resetUrl' => $resetUrl,
            'token' => 'preview-token-123',
            'message' => $mockMessage
        ]);
    }
    
    public function send(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $user->sendPasswordResetNotification('test-token-' . time());
        
        return back()->with('success', "Password reset email sent to {$user->name} ({$user->email})");
    }
}
