<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub — Login MUA</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: #ffffff;
            overflow: hidden;
        }

        /* WRAPPER */
        .login-wrapper{
            display: flex;
            min-height: 100vh;
        }

        /* LEFT SIDE IMAGE */
        .left-side{
            flex: 1;
            background-image:
                linear-gradient(
                    rgba(0,0,0,0.12),
                    rgba(0,0,0,0.12)
                ),
                url('{{ asset("assets/ba.jpg") }}');

            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            position: relative;
        }

        .left-content{
            position: absolute;
            bottom: 50px;
            left: 50px;
            color: white;
            max-width: 380px;
        }

        .left-content h1{
            font-family: 'Playfair Display', serif;
            font-size: 2.7rem;
            line-height: 1.2;
            margin-bottom: 0.8rem;
            font-weight: 700;
            text-shadow: 0 4px 18px rgba(0,0,0,0.18);
        }

        .left-content p{
            font-size: 0.95rem;
            line-height: 1.7;
            opacity: 0.95;
        }

        /* RIGHT SIDE FORM */
        .right-side{
            width: 38%;
            min-width: 470px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-card{
            width: 100%;
            max-width: 390px;
            transform: scale(0.88);
        }

        .logo{
            font-family: 'Playfair Display', serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: #4f0404;
            margin-bottom: 1.7rem;
        }

        .logo-accent{
            color: #C9A56E;
        }

        .title{
            font-size: 1.5rem;
            font-weight: 700;
            color: #2B2B2B;
            margin-bottom: 0.45rem;
        }

        .subtitle{
            color: #7A7A7A;
            font-size: 0.9rem;
            margin-bottom: 1.7rem;
            line-height: 1.6;
        }

        .form-group{
            margin-bottom: 0.95rem;
        }

        label{
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #4A3F3E;
            margin-bottom: 0.45rem;
        }

        input{
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1.5px solid #E8E2DD;
            border-radius: 14px;
            outline: none;
            font-size: 0.9rem;
            transition: 0.2s ease;
            background: #fff;
        }

        input:focus{
            border-color: #C9A56E;
            box-shadow: 0 0 0 4px rgba(201,165,110,0.12);
        }

        .remember-row{
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            margin-top: 0.2rem;
        }

        .remember-row input{
            width: auto;
            accent-color: #4f0404;
        }

        .remember-row label{
            margin: 0;
            font-weight: 400;
            font-size: 0.85rem;
            color: #7A6F6E;
        }

        .btn-login{
            width: 100%;
            padding: 0.85rem;
            background: #4f0404;
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: 0.3s ease;
        }

        .btn-login:hover{
            background: #6b0909;
        }

        .error-box{
            background: rgba(183,110,110,0.07);
            border: 1px solid rgba(183,110,110,0.3);
            color: #8A3030;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.1rem;
        }

        .register-link{
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #666;
        }

        .register-link a{
            color: #4f0404;
            font-weight: 700;
            text-decoration: none;
        }

        .register-link a:hover{
            text-decoration: underline;
        }

        .hint{
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: #4f0404;
        }

        /* RESPONSIVE */
        @media (max-width: 992px){

            body{
                overflow: auto;
            }

            .left-side{
                display: none;
            }

            .right-side{
                width: 100%;
                min-width: unset;
                padding: 30px 20px;
            }

            .login-card{
                max-width: 100%;
                transform: scale(1);
            }
        }

    </style>
</head>
<body>

    <div class="login-wrapper">

        <!-- LEFT IMAGE SECTION -->
        <div class="left-side">

            <div class="left-content">

                <h1>
                    <span style="color:white;">Join</span>
                    Beauty<span>Hub</span>
                </h1>

                <p>
                    Bangun profile MUA profesionalmu dan temukan lebih banyak client
                    dengan tampilan portfolio yang elegan dan modern.
                </p>

            </div>

        </div>

        <!-- RIGHT FORM SECTION -->
        <div class="right-side">

            <div class="login-card">

                <div class="logo">
                    Beauty<span class="logo-accent">Hub</span>
                </div>

                <div class="title">
                    Login Sebagai MUA
                </div>

                <div class="subtitle">
                    Masuk ke akun BeautyHub Anda.
                </div>

                @if($errors->any())
                <div class="error-box">
                    ⚠️ {{ $errors->first() }}
                </div>
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

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="mua@example.com"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" id="remember" name="remember">

                        <label for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn-login">
                        Masuk
                    </button>
                </form>

                <div class="register-link">
                    Belum punya akun MUA?
                    <a href="{{ route('register') }}">
                        Daftar Sekarang
                    </a>
                </div>

                <p class="hint">
                    Hanya untuk MUA & Admin BeautyHub
                </p>

            </div>

        </div>

    </div>

</body>
</html>