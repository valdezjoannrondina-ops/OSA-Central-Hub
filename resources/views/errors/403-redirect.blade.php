<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url={{ url('login') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; text-align: center; padding-top: 100px; }
        .error-box { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: inline-block; padding: 32px 48px; }
        h1 { color: #d9534f; }
        p { color: #333; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>403 Forbidden</h1>
        <p>You do not have permission to access this page.</p>
        <p>You will be redirected to the welcome page in 5 seconds.</p>
    <p><a href="{{ url('login') }}">Go now</a></p>
    </div>
</body>
</html>
