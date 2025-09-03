<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Pending - KTL Booking System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            height: 80px;
            max-width: 250px;
        }
        .title {
            color: #dc2626;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .status-good { color: #059669; }
        .status-pending { color: #d97706; }
        .contact-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/ktl_logo_email.png') }}" alt="KTL Logo">
        </div>
        
        <h1 class="title">Access Pending</h1>
        <p class="subtitle">Welcome to KTL Booking System</p>
        
        <div class="message">
            <h3 style="margin-top: 0; color: #dc2626;">Hello {{ $user->name }}!</h3>
            <p>Your account has been created successfully, but access to the system is pending administrator approval.</p>
            
            <div class="status-item">
                <span>Account Created:</span>
                <span class="status-good">✅ {{ $registeredAt->format('d M Y H:i') }}</span>
            </div>
            <div class="status-item">
                <span>Role Assignment:</span>
                <span class="{{ $hasRoles ? 'status-good' : 'status-pending' }}">
                    {{ $hasRoles ? '✅ Assigned' : '⏳ Pending' }}
                </span>
            </div>
            <div class="status-item">
                <span>Depot Access:</span>
                <span class="{{ $hasDepots ? 'status-good' : 'status-pending' }}">
                    {{ $hasDepots ? '✅ Assigned' : '⏳ Pending' }}
                </span>
            </div>
        </div>
        
        <div class="contact-info">
            <h4 style="margin-top: 0; color: #374151;">What happens next?</h4>
            <ul style="text-align: left; color: #4b5563;">
                <li>An administrator will review your account</li>
                <li>You'll be assigned appropriate roles and depot access</li>
                <li>You'll receive an email notification when access is granted</li>
                <li>You can then log in and access the booking system</li>
            </ul>
            
            <p style="margin-bottom: 0;"><strong>Need help?</strong> Contact your system administrator or IT support.</p>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" style="margin-top: 20px;">
            @csrf
            <button type="submit" class="logout-btn">Sign Out</button>
        </form>
        
        <div style="margin-top: 30px; font-size: 12px; color: #9ca3af;">
            Knowles Logistics - Always Evolving
        </div>
    </div>
</body>
</html>