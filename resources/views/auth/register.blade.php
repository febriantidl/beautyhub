<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub — Daftar MUA</title>

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
        .register-wrapper{
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

        .register-card{
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

        .logo span{
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

        .btn-register{
            width: 100%;
            padding: 0.85rem;
            background: #4f0404;
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            margin-top: 0.9rem;
            transition: 0.3s ease;
        }

        .btn-register:hover{
            background: #6b0909;
        }

        .login-text{
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.85rem;
            color: #666;
        }

        .login-text a{
            color: #4f0404;
            font-weight: 700;
            text-decoration: none;
        }

        .error-msg{
            color: #8A3030;
            font-size: 0.75rem;
            margin-top: 0.25rem;
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

            .register-card{
                max-width: 100%;
                transform: scale(1);
            }
        }

    </style>
</head>
<body>

    <div class="register-wrapper">

        <!-- LEFT IMAGE SECTION -->
        <div class="left-side">
            <div class="left-content">
                <h1>Join BeautyHub</h1>

                <p>
                    Bangun profile MUA profesionalmu dan temukan lebih banyak client
                    dengan tampilan portfolio yang elegan dan modern.
                </p>
            </div>
        </div>

        <!-- RIGHT FORM SECTION -->
        <div class="right-side">

            <div class="register-card">

                <div class="logo">
                    Beauty<span>Hub</span>
                </div>

                <div class="title">
                    Daftar Sebagai MUA
                </div>

                <div class="subtitle">
                    Lengkapi data di bawah untuk membuat akun BeautyHub.
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                        <label>Nama Lengkap</label>

                        <input
                            type="text"
                            name="name"
                            required
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap"
                        >
                    </div>

                    <div class="form-group">
                        <label>Nama Brand MUA</label>

                        <input
                            type="text"
                            name="mua_name"
                            required
                            value="{{ old('mua_name') }}"
                            placeholder="Contoh: Nayla Beauty"
                        >
                    </div>

                    <div class="form-group">
                        <label>Email</label>

                        <input
                            type="email"
                            name="email"
                            required
                            value="{{ old('email') }}"
                            placeholder="Masukkan email"
                        >
                    </div>

                    <div class="form-group">
                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Masukkan password"
                        >
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password</label>

                        <input
                            type="password"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi password"
                        >
                    </div>

                    <button type="submit" class="btn-register">
                        Daftar Sekarang
                    </button>
                </form>

                <p class="login-text">
                    Sudah punya akun?
                    <a href="{{ route('mua.login') }}">
                        Login di sini
                    </a>
                </p>

            </div>

        </div>

    </div>

</body>
</html>