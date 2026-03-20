<!DOCTYPE html>
<html lang="en">
<head>
    <title>Power Grid — New Game</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #f5f5f5; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 24px; }
        .title { font-size: 52px; font-weight: 900; margin-bottom: 8px; }
        .subtitle { font-size: 16px; color: #666; margin-bottom: 32px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 32px; width: 340px; }
        .field { margin-bottom: 20px; }
        .field label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
        .field input { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; }
        .field .hint { font-size: 12px; color: #888; margin-top: 4px; }
        .btn { width: 100%; padding: 12px; font-size: 16px; font-weight: 700; background: #4CC366; color: #fff; border: none; border-radius: 4px; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #199033; }
        .back { margin-top: 16px; font-size: 13px; color: #888; text-align: center; }
        .back a { color: #0074F0; text-decoration: none; }
        .error { background: #fee; border: 1px solid #f88; border-radius: 4px; padding: 10px 14px; margin-bottom: 20px; font-size: 13px; color: #c00; }
    </style>
</head>
<body>
<div class="page">
    <span class="title">Power Grid</span>
    <span class="subtitle">Create a new game session</span>

    <div class="card">
        @if($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error){{ $error }}<br>@endforeach
            </div>
        @endif
        <form action="/createSession" method="POST">
            @csrf
            <div class="field">
                <label>Moderator Password</label>
                <input type="password" name="passcode" placeholder="Choose a password (min 4 chars)" required autofocus>
                <span class="hint">You will use this to log in as moderator.</span>
            </div>
            <button type="submit" class="btn">Create Session →</button>
        </form>
    </div>

    <div class="back"><a href="/">← Back to home</a></div>
</div>
</body>
</html>
