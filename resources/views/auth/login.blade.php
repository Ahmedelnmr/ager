<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول — نظام إدارة الإيجارات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f4c81 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.15) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: float 8s ease-in-out infinite;
        }
        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,.1) 0%, transparent 70%);
            bottom: -100px; right: -100px;
            animation: float 6s ease-in-out infinite reverse;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }
        .login-card {
            width: 420px;
            background: rgba(255,255,255,.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
            position: relative;
            z-index: 10;
            animation: fadeUp .5s ease forwards;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .brand-icon {
            width: 70px; height: 70px;
            border-radius: 18px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; color: #fff;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(59,130,246,.4);
        }
        .login-title { 
            color: #fff; 
            font-size: 1.5rem; 
            font-weight: 700; 
            text-align: center; 
            margin-bottom: 4px;
        }
        .login-subtitle { 
            color: rgba(255,255,255,.55); 
            font-size: .9rem; 
            text-align: center; 
            margin-bottom: 32px; 
        }
        .form-label-custom {
            color: rgba(255,255,255,.8);
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }
        .form-input-custom {
            width: 100%;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 12px;
            color: #fff;
            padding: 12px 16px;
            font-family: 'Cairo', sans-serif;
            font-size: .95rem;
            transition: all .25s;
            outline: none;
        }
        .form-input-custom::placeholder { color: rgba(255,255,255,.3); }
        .form-input-custom:focus {
            border-color: #3b82f6;
            background: rgba(59,130,246,.1);
            box-shadow: 0 0 0 3px rgba(59,130,246,.2);
        }
        .input-group-custom { position: relative; }
        .input-icon {
            position: absolute;
            top: 50%; transform: translateY(-50%);
            right: 14px;
            color: rgba(255,255,255,.4);
            font-size: 1rem;
            pointer-events: none;
        }
        .form-input-custom.has-icon { padding-right: 42px; }
        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            padding: 13px;
            cursor: pointer;
            transition: all .25s;
            box-shadow: 0 4px 16px rgba(59,130,246,.35);
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(59,130,246,.5);
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }
        .btn-login:active { transform: translateY(0); }
        .error-msg {
            color: #fca5a5;
            font-size: .82rem;
            margin-top: 5px;
        }
        .alert-custom {
            background: rgba(239,68,68,.15);
            border: 1px solid rgba(239,68,68,.3);
            border-radius: 10px;
            color: #fca5a5;
            padding: 10px 14px;
            font-size: .85rem;
            margin-bottom: 20px;
        }
        .footer-text {
            color: rgba(255,255,255,.3);
            font-size: .78rem;
            text-align: center;
            margin-top: 28px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Logo -->
        <div class="brand-icon">
            <i class="bi bi-building"></i>
        </div>
        <h1 class="login-title">نظام إدارة الإيجارات</h1>
        <p class="login-subtitle">قم بتسجيل الدخول للمتابعة</p>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert-custom">{{ session('status') }}</div>
        @endif

        <!-- Errors -->
        @if ($errors->any())
            <div class="alert-custom">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="form-label-custom" for="email">البريد الإلكتروني</label>
                <div class="input-group-custom">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input-custom has-icon"
                        value="{{ old('email') }}"
                        placeholder="example@mail.com"
                        required autofocus autocomplete="username">
                    <i class="bi bi-envelope input-icon"></i>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="form-label-custom" for="password">كلمة المرور</label>
                <div class="input-group-custom">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input-custom has-icon"
                        placeholder="••••••••"
                        required autocomplete="current-password">
                    <i class="bi bi-lock input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>تسجيل الدخول
            </button>
        </form>

        <p class="footer-text">© {{ date('Y') }} Smart Rental System — جميع الحقوق محفوظة</p>
    </div>
</body>
</html>
