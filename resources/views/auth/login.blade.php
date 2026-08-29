<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEO Sign In - {{ config('app.name', 'AI Marketing Team') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-robot text-primary display-4"></i>
            <h3 class="fw-bold mt-2 font-outfit">AI Marketing Team</h3>
            <p class="text-muted small">Human-in-the-Loop AI Operating System</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label font-weight-semibold">CEO Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="ceo@aimarketing.test" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label font-weight-semibold">Password</label>
                <input type="password" class="form-control" id="password" name="password" value="password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label text-muted small" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Sign In as CEO</button>
        </form>

        <div class="mt-4 text-center">
            <span class="text-muted extra-small">Default CEO Login: <strong>ceo@aimarketing.test</strong> / <strong>password</strong></span>
        </div>
    </div>
</body>
</html>
