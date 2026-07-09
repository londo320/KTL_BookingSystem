<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Denied</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 80px auto; padding: 20px; color: #1f2937; }
        h1 { font-size: 1.5rem; }
        .message { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .switch-back { background: #fff7ed; border: 1px solid #fed7aa; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; margin-top: 10px; background: #ea580c; color: white; text-decoration: none; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; }
        .btn:hover { background: #c2410c; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
    </style>
</head>
<body>
    <h1>🚫 403 - Access Denied</h1>

    <div class="message">
        {{ $exception->getMessage() ?: 'You do not have permission to access this page.' }}
    </div>

    @auth
        @if(session('original_admin_id'))
            {{-- Whoever is currently impersonated hit a permission wall - give them a
                 direct, always-visible way back regardless of which nav (or lack of
                 one) this error page would otherwise show. --}}
            <div class="switch-back">
                <strong>You're currently testing as {{ auth()->user()->name }} ({{ auth()->user()->email }}).</strong>
                <p>This account doesn't have access to the page you tried to reach. You can switch back to your admin account below.</p>
                <form method="POST" action="{{ route('switch-back') }}">
                    @csrf
                    <button type="submit" class="btn">🔄 Switch Back to Admin</button>
                </form>
            </div>
        @else
            <p>Logged in as {{ auth()->user()->email }}.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Log Out</button>
            </form>
        @endif
    @endauth
</body>
</html>
