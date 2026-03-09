<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('auth.login') }} — {{ config('app.name', 'Smart Inventory') }}</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary-color: #4f46e5;
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            --dark-surface: #0f172a;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-surface);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background: #ffffff;
            border-radius: 2rem;
            overflow: hidden;
            display: flex;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            margin: 1.5rem;
        }

        /* Left Panel */
        .brand-panel {
            flex: 1.2;
            background: var(--primary-gradient);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(80px);
        }

        .brand-logo {
            background: rgba(255, 255, 255, 0.15);
            width: 4rem;
            height: 4rem;
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }

        .brand-logo i { font-size: 2rem; color: white; }

        .brand-content h2 {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.1;
            color: white;
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }

        .brand-content p {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 320px;
            line-height: 1.6;
        }

        /* Right Panel */
        .form-panel {
            flex: 1;
            background: white;
            padding: 4.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #1e293b;
        }

        .form-title {
            font-weight: 800;
            font-size: 2rem;
            color: #0f172a;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        .form-subtitle {
            font-size: 0.95rem;
            color: #64748b;
            margin-bottom: 2.5rem;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #94a3b8;
            margin-bottom: 0.5rem;
        }

        .input-group {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 1rem;
            padding: 0.25rem;
            transition: all 0.2s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: #94a3b8;
            padding-left: 1rem;
        }

        .form-control {
            border: none;
            background: transparent;
            padding: 0.75rem 1rem 0.75rem 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 1rem;
            border-radius: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
            filter: brightness(1.1);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
        }

        .validation-alert {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            padding: 1rem;
            border-radius: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
            color: #b91c1c;
            font-size: 0.875rem;
            font-weight: 600;
        }

        @media (max-width: 992px) {
            .login-wrapper { flex-direction: column; max-width: 500px; margin: 1rem; }
            .brand-panel { padding: 3rem; flex: none; }
            .brand-content h2 { font-size: 2.5rem; }
            .brand-content p { display: none; }
            .form-panel { padding: 3rem; }
        }

        @media (max-width: 576px) {
            .brand-panel, .form-panel { padding: 2rem; }
            .form-title { font-size: 1.5rem; }
        }

        /* Custom Animations */
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="login-wrapper fade-in-up">
        <!-- Brand Section -->
        <div class="brand-panel">
            <div class="brand-logo">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            
            <div class="brand-content">
                <h2>Smart<br>Inventory</h2>
                <p>Enterprise asset management platform built for modern organizations.</p>
            </div>

            <div class="brand-footer">
                <small style="color: rgba(255,255,255,0.4); font-weight: 600; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.2em;">
                    © {{ date('Y') }} ALL RIGHTS RESERVED
                </small>
            </div>
        </div>

        <!-- Form Section -->
        <div class="form-panel">
            <div class="mb-4">
                <h1 class="form-title">{{ __('auth.login_title') }}</h1>
                <p class="form-subtitle">{{ __('auth.login_sub') }}</p>
            </div>

            <!-- Error Alerts -->
            @if($errors->any())
            <div class="validation-alert">
                <i class="bi bi-exclamation-octagon-fill"></i>
                <div class="flex-grow-1">
                    @foreach($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label class="form-label">{{ __('auth.email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="form-label">{{ __('auth.password_label') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••">
                        <button class="btn border-0 text-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye-fill" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">{{ __('auth.remember') }}</label>
                    </div>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none small fw-bold" style="color: var(--primary-color);">Lupa Password?</a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-4">
                    {{ __('auth.login') }} <i class="bi bi-arrow-right-short ms-1" style="font-size: 1.2rem;"></i>
                </button>

                <p class="text-center mb-3" style="font-size: 0.75rem; font-weight: 700; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.1em;">
                    System Authorized Access Only
                </p>

                <p class="text-center mb-0" style="font-size: 0.85rem; font-weight: 600; color: #64748b;">
                    Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: var(--primary-color);">Register as PIC</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            eyeIcon.classList.toggle('bi-eye-fill');
            eyeIcon.classList.toggle('bi-eye-slash-fill');
        });
    </script>
</body>
</html>
