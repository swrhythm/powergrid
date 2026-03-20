<!DOCTYPE html>
<html lang="en">
<head>
    <title>Power Grid — Players Ready</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #f5f5f5; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 24px; text-align: center; }
        .title { font-size: 40px; font-weight: 900; margin-bottom: 4px; }
        .subtitle { font-size: 15px; color: #666; margin-bottom: 28px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 32px; width: 100%; max-width: 480px; text-align: left; }
        h3 { font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #199033; }
        .instr { font-size: 13px; color: #555; background: #fffbe6; border: 1px solid #f5c518; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { padding: 8px 12px; background: #f5f5f5; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #666; border-bottom: 2px solid #ddd; text-align: left; }
        td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; }
        .color-swatch { display: inline-block; width: 18px; height: 18px; border-radius: 4px; vertical-align: middle; margin-right: 6px; border: 1px solid rgba(0,0,0,0.1); }
        .id-cell { font-size: 18px; font-weight: 900; color: #0074F0; }
        .pw-cell { font-family: monospace; font-size: 15px; font-weight: 700; color: #c00; letter-spacing: 2px; }
        .btn { display: block; width: 100%; padding: 14px; font-size: 15px; font-weight: 700; background: #199033; color: #fff; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; text-align: center; transition: background 0.2s; }
        .btn:hover { background: #003EB3; }
    </style>
</head>
<body>
<div class="page">
    <span class="title">Power Grid</span>
    <span class="subtitle">Players are ready!</span>

    <div class="card">
        <h3>✅ {{ count($players) }} players created</h3>

        <div class="instr">
            📋 <strong>Share each player their ID number.</strong><br>
            All players start with the default password <strong style="color:#c00">1234</strong>.<br>
            They will be asked to change it when they first log in.
        </div>

        <table>
            <thead>
                <tr>
                    <th>Player</th>
                    <th>ID</th>
                    <th>Default Password</th>
                </tr>
            </thead>
            <tbody>
                @foreach($players as $player)
                <tr>
                    <td>
                        <span class="color-swatch" style="background:{{ $player->color }}"></span>
                        {{ $player->name }}
                    </td>
                    <td class="id-cell">{{ $player->id }}</td>
                    <td class="pw-cell">1234</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <a href="/playerDetail?id={{ $moderatorId }}&passcode={{ urlencode($moderatorPasscode) }}" class="btn">
            Go to Moderator View →
        </a>
    </div>
</div>
</body>
</html>
