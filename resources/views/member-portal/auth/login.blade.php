<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login | Inabanga Wellness Center</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: grid; place-items: center;
            padding: 24px; font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #071426, #102b50);
            color: #172b4d;
        }
        .panel {
            width: 100%; max-width: 440px; padding: 36px;
            background: white; border-radius: 18px;
            box-shadow: 0 20px 60px #0005;
        }
        .brand { color: #123d70; font-size: 13px; font-weight: bold; letter-spacing: 2px; }
        h1 { margin-bottom: 8px; font-size: 28px; }
        .muted { color: #66758a; line-height: 1.5; }
        label { display: block; margin: 20px 0 8px; font-weight: bold; font-size: 14px; }
        input {
            width: 100%; padding: 13px; border: 1px solid #ccd5e0;
            border-radius: 9px; font-size: 15px;
        }
        input:focus { outline: 2px solid #4d8fe8; border-color: transparent; }
        button {
            width: 100%; padding: 14px; margin-top: 22px;
            border: 0; border-radius: 9px; background: #174c87;
            color: white; font-weight: bold; font-size: 15px; cursor: pointer;
        }
        button:hover { background: #103862; }
        a { color: #174c87; font-weight: bold; text-decoration: none; }
        .message { padding: 12px; border-radius: 8px; margin: 15px 0; font-size: 14px; }
        .error { background: #fff0f0; color: #a52222; }
        .success { background: #eaf8ef; color: #176534; }
        .links { text-align: center; margin-top: 22px; line-height: 2; font-size: 14px; }
        .remember { display: flex; align-items: center; gap: 8px; margin-top: 16px; font-size: 14px; }
        .remember input { width: auto; }
    </style>
</head>
<body>
    <main class="panel">
        <div class="brand">INABANGA WELLNESS CENTER</div>
        <h1>Member Portal</h1>
        <p class="muted">Sign in to view your membership, attendance, and payment history.</p>

        @if (session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="message error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('member.login.store') }}">
            @csrf

            <label for="email">Registered email address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username">

            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password">

            <label class="remember">
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>

            <button type="submit">Sign In</button>
        </form>

        <div class="links">
            <div>Your gym administrator must create your member account.</div>
            <div><a href="{{ url('/login') }}">Staff login</a></div>
        </div>
    </main>
</body>
</html>
