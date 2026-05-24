<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub — Daftar MUA</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #FAF0F0 0%, #FFF8F0 50%, #F0F4FA 100%); padding: 20px; }
        .register-card { background: white; border-radius: 28px; padding: 2.5rem; width: 100%; max-width: 450px; box-shadow: 0 20px 60px rgba(155,107,107,0.12); border: 1px solid rgba(212,165,165,0.2); }
        .logo { font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 700; color: #9B6B6B; text-align: center; margin-bottom: 1.5rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-weight: 600; font-size: 0.85rem; color: #4A3F3E; margin-bottom: 0.4rem; }
        input { width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #E8E2DD; border-radius: 14px; outline: none; font-size: 0.9rem; }
        input:focus { border-color: #D4A5A5; }
        .btn-register { width: 100%; padding: 0.875rem; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; margin-top: 1rem; }
        .error-msg { color: #8A3030; font-size: 0.75rem; margin-top: 0.2rem; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="logo">Beauty<span style="color: #C9A56E;">Hub</span></div>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label>Nama Brand MUA</label>
                <input type="text" name="mua_name" required value="{{ old('mua_name') }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            
            <button type="submit" class="btn-register">Daftar Sekarang</button>
        </form>

        <p style="text-align: center; margin-top: 1rem; font-size: 0.85rem;">
            Sudah punya akun? <a href="{{ route('mua.login') }}" style="color: #9B6B6B; font-weight: 700;">Login di sini</a>
        </p>
    </div>
</body>
</html>