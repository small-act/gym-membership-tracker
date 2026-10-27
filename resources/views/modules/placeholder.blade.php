<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Gym Membership Tracker</title>
    <style>
        body {
            margin: 0;
            padding: 40px;
            background: #081426;
            color: #f1f5f9;
            font-family: Arial, sans-serif;
        }
        .panel {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            background: #11213a;
            border: 1px solid #263a56;
            border-radius: 14px;
        }
        a {
            color: #7dd3fc;
            text-decoration: none;
        }
        .back {
            display: inline-block;
            margin-bottom: 25px;
        }
        p { color: #b8c5d8; line-height: 1.7; }
    </style>
</head>
<body>
    <div class="panel">
        <a class="back" href="{{ route('dashboard') }}">← Back to Dashboard</a>
        <h1>{{ $title }}</h1>
        <p>This section is ready for its module implementation.</p>
        <p>Use the dashboard link above to return to the main screen.</p>
    </div>
</body>
</html>
