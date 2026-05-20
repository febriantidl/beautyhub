<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login MUA - BeautyHub</title>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: linear-gradient(135deg, #FAF8F6 0%, #F5F3F1 100%); margin: 0; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { background: white; padding: 2rem; border-radius: 20px; box-shadow: 0 20px 40px rgba(156,107,107,0.15); width: 100%; max-width: 400px; }
        .logo { font-family: 'Playfair Display', serif; font-size: 32px; text-align: center; margin-bottom: 2rem; color: #9B6B6B; }
        .logo-accent { color: #C9A56E; }
        input { width: 100%; padding: 12px; margin-bottom: 1rem; border: 1px solid #E8E2DD; border-radius: 12px; }
        button { width: 100%; padding: 12px; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; border: none; border-radius: 12px; font-weight: bold; cursor: pointer; }
        .error { color: #B76E6E; font-size: 14px; margin-top: -0.5rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        <h3 style="text-align: center; margin-bottom: 1.5rem;">Login MUA</h3>
        <form method="POST" action="{{ route('mua.login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Masuk</button>
        </form>
        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
    </div>
</body>
</html>