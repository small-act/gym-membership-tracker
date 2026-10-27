<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f9d9e6">
    <title>Log In | Inabanga Wellness Center</title>

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9d9e6;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #15152b;
        }

        .login-frame {
            width: min(1400px, 100%);
            min-height: 690px;
            padding: 10px;
            border: 5px solid rgba(255, 255, 255, .92);
            border-radius: 36px;
            background: linear-gradient(125deg, #d66bd9, #8583f7 48%, #f5a8e0);
            box-shadow: 0 18px 60px rgba(115, 70, 130, .12);
        }

        .login-scene {
            min-height: 660px;
            display: grid;
            grid-template-columns: minmax(0, 48%) minmax(0, 1fr);
            border-radius: 25px;
            background:
                linear-gradient(120deg, rgba(239, 180, 244, .15), rgba(115, 153, 255, .15)),
                url('/images/fitness-login.png') center / cover no-repeat,
                linear-gradient(120deg, #eaa5e9, #8e94ff 58%, #8bbcff);
        }

        .login-panel {
            min-width: 0;
            margin: 24px;
            padding: 38px 36px 24px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 28px;
            background: rgba(255, 255, 255, .88);
            -webkit-backdrop-filter: blur(16px);
            backdrop-filter: blur(16px);
        }

        .brand {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }

        .brand-mark {
            font-size: 52px;
            line-height: 1;
            font-weight: 400;
            font-style: italic;
            color: #17172d;
        }

        .brand-divider {
            width: 1px;
            height: 45px;
            background: #55536b;
        }

        .brand-name {
            font-size: 21px;
            font-weight: 800;
            letter-spacing: 4px;
        }

        .brand-subtitle {
            margin-top: 3px;
            font-size: 12px;
            font-weight: 500;
        }

        h1 {
            margin: 0 0 24px;
            text-align: center;
            font-size: 25px;
            font-weight: 750;
            color: #11111b;
        }

        .session-status {
            margin-bottom: 16px;
            color: #276749;
            font-size: 13px;
            text-align: center;
        }

        .error-box {
            margin-bottom: 16px;
            padding: 10px 12px;
            border: 1px solid #fecaca;
            border-radius: 8px;
            background: #fff1f2;
            color: #b91c1c;
            font-size: 13px;
        }

        .error-box div + div {
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .form-input {
            display: block;
            width: 100%;
            min-width: 0;
            height: 52px;
            padding: 0 15px;
            border: 1px solid #dcddec;
            border-radius: 8px;
            background: #f5f7fb;
            color: #20233a;
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-input:focus {
            border-color: #9180ee;
            box-shadow: 0 0 0 3px rgba(145, 128, 238, .18);
        }

        .form-input::placeholder {
            color: #858b9a;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap .form-input {
            padding-right: 54px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            min-width: 38px;
            min-height: 38px;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #667085;
            cursor: pointer;
            font-size: 18px;
        }

        .toggle-password:hover {
            background: #eceaf8;
        }

        .form-options {
            margin: 5px 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #45465d;
            cursor: pointer;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: #8275e9;
        }

        a {
            color: #45465d;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .login-buttons {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 12px;
            width: 100%;
        }

        .login-button {
            width: 100%;
            min-width: 0;
            min-height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px 14px;
            border: 0;
            border-radius: 8px;
            background: #090909;
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.4;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            touch-action: manipulation;
            -webkit-tap-highlight-color: rgba(117, 85, 206, .2);
            transition: background .2s, box-shadow .2s;
        }

        .login-button:hover {
            background: #30234f;
            color: #fff;
            text-decoration: none;
        }

        .login-button:focus-visible {
            outline: 3px solid #9180ee;
            outline-offset: 3px;
        }

        .login-button:active {
            transform: translateY(1px);
        }

        .member-login-container {
            margin-top: 12px;
        }

        .member-login-button {
            background: #7555ce;
        }

        .member-login-button:hover {
            background: #5c3caf;
        }

        .panel-footer {
            margin-top: auto;
            padding-top: 28px;
            text-align: center;
            color: #59566e;
            font-size: 11px;
        }

        .panel-footer a {
            font-weight: 600;
        }

        .footer-separator {
            padding: 0 7px;
        }

        @media (max-width: 900px) {
            body {
                padding: 16px;
                align-items: flex-start;
            }

            .login-frame {
                min-height: auto;
                border-radius: 28px;
            }

            .login-scene {
                min-height: auto;
                grid-template-columns: minmax(0, 1fr);
                background-position: center;
            }

            .login-panel {
                width: auto;
                margin: 18px;
                padding: 36px 28px 24px;
                min-height: 580px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 8px;
            }

            .login-frame {
                padding: 5px;
                border-width: 3px;
                border-radius: 22px;
            }

            .login-scene {
                border-radius: 16px;
            }

            .login-panel {
                margin: 10px;
                padding: 30px 20px 22px;
                min-height: 550px;
                border-radius: 18px;
            }

            .brand {
                gap: 9px;
                margin-bottom: 30px;
            }

            .brand-mark {
                font-size: 43px;
            }

            .brand-divider {
                height: 40px;
            }

            .brand-name {
                font-size: 17px;
                letter-spacing: 2px;
            }

            .brand-subtitle {
                font-size: 11px;
            }

            h1 {
                font-size: 23px;
            }

            .panel-footer {
                padding-top: 24px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <main class="login-frame">
        <div class="login-scene">
            <section class="login-panel" aria-labelledby="loginHeading">
                <div class="brand">
                    <div class="brand-mark">IWC</div>
                    <div class="brand-divider" aria-hidden="true"></div>
                    <div>
                        <div class="brand-name">WELLNESS</div>
                        <div class="brand-subtitle">INABANGA CENTER</div>
                    </div>
                </div>

                <h1 id="loginHeading">Log in</h1>

                @if (session('status'))
                    <div class="session-status" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error-box" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="Email"
                            required
                            autofocus
                            autocomplete="username"
                            aria-label="Email"
                        >
                    </div>

                    <div class="form-group">
                        <div class="password-wrap">
                            <input
                                id="password"
                                class="form-input"
                                type="password"
                                name="password"
                                placeholder="Password"
                                required
                                autocomplete="current-password"
                                aria-label="Password"
                            >

                            <button
                                class="toggle-password"
                                type="button"
                                id="togglePassword"
                                aria-label="Show password"
                                aria-pressed="false"
                                title="Show or hide password"
                            >◉</button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="remember" for="remember_me">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <span>Keep me logged in</span>
                        </label>
                    </div>

                    <div class="login-buttons">
                        <button type="submit" class="login-button">
                            Log In
                        </button>
                    </div>
                </form>

                <div class="login-buttons member-login-container">
                    <a
                        href="{{ route('member.login') }}"
                        class="login-button member-login-button"
                        id="memberLoginButton"
                        aria-label="Open Member Login page"
                    >
                        Member Login
                    </a>
                </div>

                <footer class="panel-footer">
                    <a href="#" onclick="return false;">Terms of Use</a>
                    <span class="footer-separator">|</span>
                    <a href="#" onclick="return false;">Privacy Policy</a>
                </footer>
            </section>
        </div>
    </main>

    <script>
        (() => {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('togglePassword');

            if (!passwordInput || !togglePassword) {
                return;
            }

            togglePassword.addEventListener('click', () => {
                const showPassword = passwordInput.type === 'password';

                passwordInput.type = showPassword ? 'text' : 'password';
                togglePassword.textContent = showPassword ? '◎' : '◉';
                togglePassword.setAttribute(
                    'aria-label',
                    showPassword ? 'Hide password' : 'Show password'
                );
                togglePassword.setAttribute(
                    'aria-pressed',
                    showPassword ? 'true' : 'false'
                );
            });
        })();
    </script>
</body>
</html>