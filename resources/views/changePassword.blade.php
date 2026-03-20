<!DOCTYPE html>
<html lang="en">
<head>
    <title>Power Grid — Change Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: {{ $player->color }}22; min-height: 100vh; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 24px; }
        .avatar { width: 56px; height: 56px; border-radius: 50%; background: {{ $player->color }}; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); margin-bottom: 12px; }
        .player-name { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .player-hint { font-size: 13px; color: #666; margin-bottom: 24px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 32px; width: 340px; }
        h3 { font-size: 17px; font-weight: 700; margin-bottom: 4px; }
        .card-hint { font-size: 13px; color: #888; margin-bottom: 20px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: #444; }
        .field input { width: 100%; padding: 9px 12px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .btn { width: 100%; padding: 12px; font-size: 15px; font-weight: 700; background: {{ $player->color }}; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 8px; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .error { background: #fee; border: 1px solid #f88; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #c00; }
    </style>
</head>
<body>
<div class="page">
    <div class="avatar"></div>
    <div class="player-name">{{ $player->name }}</div>
    <div class="player-hint">Welcome! Please set a new password before continuing.</div>

    <div class="card">
        @if($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error){{ $error }}<br>@endforeach
            </div>
        @endif

        <h3>Change Password</h3>
        <p class="card-hint">Choose a new password (minimum 4 characters).</p>

        <form action="/changePassword" method="POST">
            @csrf
            <input type="hidden" name="playerId" value="{{ $player->id }}">
            <input type="hidden" name="currentPasscode" value="{{ $player->passCode }}">

            <div class="field">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="New password" required autofocus>
            </div>
            <div class="field">
                <label>Confirm Password</label>
                <input type="password" name="new_password_confirmation" placeholder="Repeat password" required>
            </div>
            <button type="submit" class="btn">Set Password →</button>
        </form>
    </div>
</div>
</body>
</html>
