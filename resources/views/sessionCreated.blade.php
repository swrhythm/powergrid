<!DOCTYPE html>
<html lang="en">
<head>
    <title>Power Grid — Session Ready</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #f5f5f5; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 24px; text-align: center; }
        .title { font-size: 52px; font-weight: 900; margin-bottom: 8px; }
        .subtitle { font-size: 16px; color: #666; margin-bottom: 32px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 40px 48px; max-width: 420px; width: 100%; }
        .id-box { background: #f0faf4; border: 3px solid #4CC366; border-radius: 12px; padding: 24px; margin: 24px 0; }
        .id-label { font-size: 14px; color: #555; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .id-number { font-size: 72px; font-weight: 900; color: #199033; line-height: 1; }
        .warning { font-size: 13px; color: #c00; font-weight: 600; margin-top: 8px; }
        .info { font-size: 14px; color: #555; margin: 8px 0; }
        .btn { display: block; width: 100%; padding: 14px; font-size: 16px; font-weight: 700; background: #199033; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; margin-top: 24px; transition: background 0.2s; }
        .btn:hover { background: #003EB3; }
    </style>
</head>
<body>
<div class="page">
    <span class="title">Power Grid</span>
    <span class="subtitle">Session created!</span>

    <div class="card">
        <p class="info">Your moderator session is ready. Write down your ID:</p>

        <div class="id-box">
            <div class="id-label">Your Moderator ID</div>
            <div class="id-number">{{ $mod->id }}</div>
        </div>

        <p class="warning">⚠ Write this number down — you need it to log in!</p>
        <p class="info" style="margin-top:12px">Password: <strong>{{ $mod->passCode }}</strong></p>

        <a href="/viewPlayer" class="btn">Continue to Login →</a>
    </div>
</div>
</body>
</html>
