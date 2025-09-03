<!DOCTYPE html>
<html>
<head>
    <title>Email Testing - KTL Booking System</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .user-list { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .user-item { margin: 10px 0; padding: 10px; background: white; border-radius: 4px; }
        .buttons { display: flex; gap: 10px; margin-top: 10px; }
        button { padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
        .preview-btn { background: #3b82f6; color: white; }
        .send-btn { background: #dc2626; color: white; }
        .alert { padding: 15px; margin: 10px 0; border-radius: 4px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Email Testing - KTL Booking System</h1>
        
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        
        <div class="user-list">
            <h2>Test Users</h2>
            @foreach($users as $user)
                <div class="user-item">
                    <strong>{{ $user->name }}</strong> - {{ $user->email }}
                    <div class="buttons">
                        <form method="GET" action="{{ route('app.test-email.preview') }}" style="display: inline;">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="preview-btn">👁️ Preview Email</button>
                        </form>
                        <form method="POST" action="{{ route('app.test-email.send') }}" style="display: inline;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="send-btn">📧 Send Test Email</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
            <h3>📋 Instructions</h3>
            <ul>
                <li><strong>Preview Email:</strong> See exactly how the email will look in the browser</li>
                <li><strong>Send Test Email:</strong> Send actual email to mailtrap for testing</li>
                <li><strong>Logo:</strong> Replace <code>/public/images/ktl_logo_email.png</code> with your KTL logo</li>
                <li><strong>Mailtrap:</strong> Check your mailtrap inbox for received emails</li>
            </ul>
        </div>
    </div>
</body>
</html>