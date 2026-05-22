<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | BeautyHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #FAF0F0, #FFF8F0); margin: 0; text-align: center; padding: 2rem; }
        .code { font-family: 'Playfair Display', serif; font-size: 8rem; font-weight: 700; color: #D4A5A5; line-height: 1; margin-bottom: 1rem; }
        h1 { font-size: 1.75rem; font-weight: 700; color: #2C2423; margin-bottom: 0.75rem; }
        p { color: #7A6F6E; margin-bottom: 2rem; }
        a { display: inline-block; padding: 0.75rem 2rem; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; text-decoration: none; border-radius: 14px; font-weight: 600; }
    </style>
</head>
<body>
    <div>
        <div class="code">500</div>
        <h1>Terjadi Kesalahan Server</h1>
        <p>Mohon maaf, terjadi kesalahan di server kami. Tim kami sedang menangani masalah ini.</p>
        <a href="{{ route('mua.login') }}">← Kembali ke Login</a>
    </div>
</body>
</html>
