<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration | Inabanga Wellness Center</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: grid; place-items: center;
            padding: 24px; font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #071426, #102b50);
        }
        .panel {
            width: 100%; max-width: 460px; padding: 36px;
            background: white; border-radius: 18px; color: #172b4d;
            box-shadow: 0 20px 60px #0005;
        }
        .brand { color: #123d70; font-size: 13px; font-weight: bold; letter-spacing: 2px; }
        h1 { margin-bottom: 8px; }
        p { color: #66758a; line-height: 1.5; }
        label { display: block; margin: 18px 0 7px; font-weight: bold; font-size: 14px; }
        input {
            width: 100%; padding: 13px; border: 1px solid #ccd5e0;
            border-radius: 9px; font-size: 15px;
        }
        button {
            width: 100%; padding: 14px; margin-top: 22px;
            border: 0; border-radius: 9px; background: #174c87;
            color: white; font-size: 15px; font-weight: bold; cursor: pointer;
        }
        a { color: #174c87; font-weight: bold; text-decoration: none; }
        .error { background: #fff0f0; color: #a52222; padding: 12px; border-radius: 8px; }
        .links { text-align: center; margin-top: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <main class="panel">
        <div class="brand">INABANGA WELLNESS CENTER</div>
        <h1>Create Member Account</h1>
        <p>Use the email address already registered with the gym. Your account will be linked to your existing membership records.</p>

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('member.register.store') }}">
            @csrf

            <label for="email">Registered email address</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}" required autocomplete="email">

            <label for="password">Create password</label>
            <input id="password" type="password" name="password"
                   minlength="12" required autocomplete="new-password">
            <p>Use at least 12 characters.</p>

            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password"
                   name="password_confirmation" minlength="12"
                   required autocomplete="new-password">

            <button type="submit">Create Account</button>
        </form>

        <div class="links">
            Already registered? <a href="{{ route('member.login') }}">Sign in</a>
        </div>
    </main>
</body>
</html>
