<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Page Expired</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            text-align: center;
            padding-top: 100px;
        }
        .error-box {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: inline-block;
            padding: 32px 48px;
            max-width: 500px;
        }
        h1 {
            color: #d9534f;
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        p {
            color: #333;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            margin: 0.5rem;
            background-color: midnightblue;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1a237e;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>419 Page Expired</h1>
        <p>Your session has expired or the page token is invalid.</p>
        <p>This usually happens when:</p>
        <ul style="text-align: left; display: inline-block; margin: 1rem 0;">
            <li>The page was open for too long (over 2 hours)</li>
            <li>You have multiple tabs open</li>
            <li>Your session expired</li>
        </ul>
        <div>
            <a href="{{ route('login') }}" class="btn">Go to Login</a>
            <button onclick="window.location.reload()" class="btn btn-secondary">Refresh Page</button>
        </div>
    </div>
    <script>
        // Auto-refresh CSRF token and try to continue
        document.addEventListener('DOMContentLoaded', function() {
            // Try to refresh the page once after a short delay
            setTimeout(function() {
                // Check if we can refresh the session
                fetch(window.location.href, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                }).then(function(response) {
                    if (response.ok) {
                        // Session might be refreshed, try to reload
                        window.location.reload();
                    }
                }).catch(function(error) {
                    console.log('Session refresh failed, user needs to login');
                });
            }, 2000);
        });
    </script>
</body>
</html>

