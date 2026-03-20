<!DOCTYPE html>
<html lang="en">
<head>
    <title>Power Grid — Setup Players</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Inter, sans-serif; background: #f5f5f5; }
        .page { min-height: 100vh; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 24px; }
        .title { font-size: 40px; font-weight: 900; margin-bottom: 4px; }
        .subtitle { font-size: 15px; color: #666; margin-bottom: 28px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 32px; width: 100%; max-width: 480px; }
        h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #222; }
        .step { display: none; }
        .step.active { display: block; }
        .count-select { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .count-btn { flex: 1; min-width: 60px; padding: 16px 0; font-size: 22px; font-weight: 700; border: 2px solid #ddd; border-radius: 8px; background: #fafafa; cursor: pointer; text-align: center; transition: all 0.15s; }
        .count-btn:hover, .count-btn.selected { border-color: #4CC366; background: #f0faf4; color: #199033; }
        .player-row { display: flex; gap: 10px; align-items: center; margin-bottom: 12px; }
        .player-num { font-size: 14px; font-weight: 700; color: #888; width: 24px; flex-shrink: 0; }
        .player-row input[type=text] { flex: 1; padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .player-row select { padding: 8px 10px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        .color-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }
        .btn { width: 100%; padding: 12px; font-size: 15px; font-weight: 700; background: #4CC366; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px; transition: background 0.2s; }
        .btn:hover { background: #199033; }
        .btn-back { background: #eee; color: #333; margin-top: 8px; }
        .btn-back:hover { background: #ccc; }
        .error { background: #fee; border: 1px solid #f88; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #c00; }
        #playerRows { margin-top: 16px; }
    </style>
</head>
<body>
<div class="page">
    <span class="title">Power Grid</span>
    <span class="subtitle">Set up players for your game</span>

    <div class="card">
        @if($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error){{ $error }}<br>@endforeach
            </div>
        @endif

        {{-- Step 1: pick number of players --}}
        <div class="step active" id="step1">
            <h3>Step 1 — How many players?</h3>
            <div class="count-select">
                @foreach([2,3,4,5,6] as $n)
                    <div class="count-btn" onclick="selectCount({{ $n }})">{{ $n }}</div>
                @endforeach
            </div>
        </div>

        {{-- Step 2: fill in names and colors --}}
        <div class="step" id="step2">
            <h3>Step 2 — Player names & colors</h3>
            <form action="/setupPlayers" method="POST" id="playersForm">
                @csrf
                <input type="hidden" name="moderatorId" value="{{ $moderatorId }}">
                <input type="hidden" name="moderatorPasscode" value="{{ $moderatorPasscode }}">
                <input type="hidden" name="player_count" id="playerCountInput" value="">

                <div id="playerRows"></div>

                <button type="submit" class="btn">Create Players →</button>
            </form>
            <button class="btn btn-back" onclick="goBack()">← Change count</button>
        </div>
    </div>
</div>

<script>
    var selectedCount = 0;
    var colors = [
        { value: 'blue',   label: 'Blue',   bg: '#4a90d9' },
        { value: 'yellow', label: 'Yellow', bg: '#f5c518' },
        { value: 'red',    label: 'Red',    bg: '#e53935' },
        { value: 'black',  label: 'Black',  bg: '#222222' },
        { value: 'purple', label: 'Purple', bg: '#8e24aa' },
        { value: 'green',  label: 'Green',  bg: '#43a047' },
    ];

    function selectCount(n) {
        selectedCount = n;
        document.querySelectorAll('.count-btn').forEach(function(btn) {
            btn.classList.toggle('selected', parseInt(btn.textContent) === n);
        });
        document.getElementById('playerCountInput').value = n;
        buildRows(n);
        document.getElementById('step1').classList.remove('active');
        document.getElementById('step2').classList.add('active');
    }

    function buildRows(n) {
        var rows = document.getElementById('playerRows');
        rows.innerHTML = '';
        var usedColors = [];
        for (var i = 0; i < n; i++) {
            var defaultColor = colors[i % colors.length].value;
            usedColors.push(defaultColor);

            var row = document.createElement('div');
            row.className = 'player-row';

            var num = document.createElement('span');
            num.className = 'player-num';
            num.textContent = (i + 1);

            var nameInput = document.createElement('input');
            nameInput.type = 'text';
            nameInput.name = 'name[' + i + ']';
            nameInput.placeholder = 'Player ' + (i + 1) + ' name';
            nameInput.required = true;

            var colorSelect = document.createElement('select');
            colorSelect.name = 'color[' + i + ']';
            colors.forEach(function(c) {
                var opt = document.createElement('option');
                opt.value = c.value;
                opt.textContent = c.label;
                if (c.value === defaultColor) opt.selected = true;
                colorSelect.appendChild(opt);
            });

            row.appendChild(num);
            row.appendChild(nameInput);
            row.appendChild(colorSelect);
            rows.appendChild(row);
        }
    }

    function goBack() {
        document.getElementById('step2').classList.remove('active');
        document.getElementById('step1').classList.add('active');
    }

    // If page reloaded with errors, restore step 2 if player_count was posted
    @if(old('player_count'))
        selectCount({{ old('player_count') }});
    @endif
</script>
</body>
</html>
