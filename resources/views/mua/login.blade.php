<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub — Login MUA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #FAF0F0 0%, #FFF8F0 50%, #F0F4FA 100%);
        }
        .login-card {
            background: white;
            border-radius: 28px;
            padding: 2.75rem 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(155,107,107,0.12);
            border: 1px solid rgba(212,165,165,0.2);
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #9B6B6B;
            text-align: center;
            margin-bottom: 0.4rem;
        }
        .logo-accent { color: #C9A56E; }
        .subtitle {
            text-align: center;
            color: #9B8F8E;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }
        .form-group { margin-bottom: 1.1rem; }
        label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #4A3F3E;
            margin-bottom: 0.45rem;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #E8E2DD;
            border-radius: 14px;
            font-family: inherit;
            font-size: 0.9rem;
            color: #2C2423;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        input:focus {
            border-color: #D4A5A5;
            box-shadow: 0 0 0 3px rgba(212,165,165,0.15);
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.4rem;
        }
        .remember-row input { width: auto; accent-color: #9B6B6B; }
        .remember-row label { margin: 0; font-weight: 400; font-size: 0.85rem; color: #7A6F6E; }
        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #D4A5A5, #9B6B6B);
            color: white;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(155,107,107,0.3);
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(155,107,107,0.4); }
        .error-box {
            background: rgba(183,110,110,0.07);
            border: 1px solid rgba(183,110,110,0.3);
            color: #8A3030;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.1rem;
        }
        .register-link {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
        }
        .register-link a {
            color: #9B6B6B;
            font-weight: 700;
            text-decoration: none;
        }
        .register-link a:hover { text-decoration: underline; }
        .hint {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: #B0A8A7;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        <p class="subtitle">Dashboard MUA — Masuk ke akun Anda</p>

        @if($errors->any())
        <div class="error-box">⚠️ {{ $errors->first() }}</div>
        @endif

        @if(session('success'))
        <div style="background:rgba(100,160,100,0.07);border:1px solid rgba(100,160,100,0.3);color:#2E6A2E;border-radius:12px;padding:.75rem 1rem;font-size:.85rem;margin-bottom:1.1rem;">
            ✅ {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('mua.login.submit') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="mua@example.com" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn-login">Masuk →</button>
        </form>

        <div class="register-link">
            Belum punya akun MUA? 
            <a href="{{ route('register') }}">Daftar Sekarang</a>
        </div>

        <p class="hint">Hanya untuk MUA & Admin BeautyHub</p>
    </div>
</body>
</html>