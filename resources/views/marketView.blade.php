<!DOCTYPE html>
<html lang="en">
<head>
    <title>Powergrid — Market</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #1a1a2e;
            color: #e0e0e0;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
        }
        h1 { font-size: 2rem; color: #f0c040; text-align: center; margin-bottom: 4px; }
        .subtitle { text-align: center; color: #aaa; margin-bottom: 20px; font-size: 1rem; }
        .step-badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 1rem;
        }
        .step-1 { background: #555; color: #eee; }
        .step-2 { background: #8B6914; color: #ffe; }
        .step-3 { background: #8B1414; color: #fee; }

        /* ---- MARKET BOARD ---- */
        .market-board {
            background: #16213e;
            border: 2px solid #0f3460;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .market-board h2 { font-size: 1.2rem; color: #f0c040; margin-bottom: 14px; letter-spacing: 2px; text-transform: uppercase; }
        .resource-row {
            display: flex;
            align-items: center;
            margin-bottom: 14px;
        }
        .resource-label {
            width: 110px;
            font-weight: bold;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .slots-wrap { display: flex; flex-wrap: wrap; gap: 4px; }
        .slot {
            width: 38px;
            height: 38px;
            border-radius: 6px;
            border: 2px solid rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: bold;
            position: relative;
            flex-direction: column;
        }
        .slot-count { font-size: 1.1rem; line-height: 1; }
        .slot-price { font-size: 0.6rem; color: rgba(255,255,255,0.5); line-height: 1; }
        .slot-full    { border-color: rgba(255,255,255,0.6); }
        .slot-partial { border-color: rgba(255,255,255,0.3); opacity: 0.85; }
        .slot-empty   { border-color: rgba(255,255,255,0.1); opacity: 0.4; }
        .slot-unavail { opacity: 0.1; border-style: dashed; }

        /* Resource colors */
        .coal-row    .resource-label { color: #c4904a; }
        .coal-row    .slot-full    { background: #8B4513; }
        .coal-row    .slot-partial { background: #6B3410; }
        .coal-row    .slot-empty   { background: #3a2010; }

        .oil-row     .resource-label { color: #aaa; }
        .oil-row     .slot-full    { background: #444; }
        .oil-row     .slot-partial { background: #333; }
        .oil-row     .slot-empty   { background: #222; }

        .garbage-row .resource-label { color: #DAA520; }
        .garbage-row .slot-full    { background: #8B7500; }
        .garbage-row .slot-partial { background: #6B5800; }
        .garbage-row .slot-empty   { background: #3a3000; }

        .uranium-row .resource-label { color: #ff6666; }
        .uranium-row .slot-full    { background: #8B0000; }
        .uranium-row .slot-partial { background: #6B0000; }
        .uranium-row .slot-empty   { background: #300000; }

        /* ---- PLAYER TABLE ---- */
        .players-board {
            background: #16213e;
            border: 2px solid #0f3460;
            border-radius: 12px;
            padding: 20px;
        }
        .players-board h2 { font-size: 1.2rem; color: #f0c040; margin-bottom: 14px; letter-spacing: 2px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 6px 10px; color: #f0c040; border-bottom: 1px solid #0f3460; font-size: 0.85rem; text-align: left; }
        td { padding: 8px 10px; border-bottom: 1px solid #0f3460; font-size: 0.95rem; vertical-align: middle; }
        .color-dot { display: inline-block; width: 14px; height: 14px; border-radius: 50%; margin-right: 6px; vertical-align: middle; border: 1px solid rgba(255,255,255,0.3); }
        .pp-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.78rem;
            margin: 1px;
            font-weight: bold;
        }
        .pp-coal    { background: #8B4513; color: #fff; }
        .pp-oil     { background: #444; color: #fff; }
        .pp-garbage { background: #8B7500; color: #fff; }
        .pp-uranium { background: #8B0000; color: #fff; }
        .pp-hybrid  { background: #445; color: #fff; }
        .pp-eco     { background: #1a4a1a; color: #7f7; }
        .pp-unknown { background: #555; color: #fff; }
        .no-session {
            text-align: center;
            padding: 40px;
            color: #888;
        }
        .refresh-note { text-align: right; font-size: 0.75rem; color: #555; margin-top: 8px; }
    </style>
</head>
<body>

<h1>POWERGRID</h1>
<p class="subtitle">
    @if($session)
        <span class="step-badge step-{{ $session->step }}">Step {{ $session->step }}</span>
        &nbsp;&nbsp;{{ $session->player_count }} Players
    @else
        No game session active
    @endif
</p>

@if($session)

{{-- ===== RESOURCE MARKET ===== --}}
<div class="market-board">
    <h2>Resource Market</h2>

    @php
        $marketRows = [
            'coal'    => ['label' => '🪨 Coal',    'slots' => $session->coal_slots,    'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 0],
            'oil'     => ['label' => '🛢 Oil',     'slots' => $session->oil_slots,     'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 2],
            'garbage' => ['label' => '🗑 Garbage', 'slots' => $session->garbage_slots, 'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 6],
            'uranium' => ['label' => '☢ Uranium',  'slots' => $session->uranium_slots, 'maxSlot' => 16, 'maxPer' => 1, 'minIdx' => 0],
        ];
    @endphp

    @foreach($marketRows as $type => $cfg)
    <div class="resource-row {{ $type }}-row">
        <div class="resource-label">{{ $cfg['label'] }}</div>
        <div class="slots-wrap">
            @for($i = 0; $i < $cfg['maxSlot']; $i++)
                @if($i < $cfg['minIdx'])
                    <div class="slot slot-unavail">
                        <span class="slot-count">—</span>
                        <span class="slot-price">{{ $i+1 }}E</span>
                    </div>
                @else
                    @php
                        $count = $cfg['slots'][$i] ?? 0;
                        $sc = $count == $cfg['maxPer'] ? 'slot-full' : ($count > 0 ? 'slot-partial' : 'slot-empty');
                    @endphp
                    <div class="slot {{ $sc }}">
                        <span class="slot-count">{{ $count > 0 ? $count : '·' }}</span>
                        <span class="slot-price">{{ $i+1 }}E</span>
                    </div>
                @endif
            @endfor
        </div>
    </div>
    @endforeach
</div>

{{-- ===== PLAYER STANDINGS ===== --}}
<div class="players-board">
    <h2>Players</h2>
    @if(count($playerList) > 0)
    <table>
        <thead>
            <tr>
                <th>Player</th>
                <th>Cash</th>
                <th>Houses</th>
                <th>Powerplants</th>
            </tr>
        </thead>
        <tbody>
            @foreach($playerList as $p)
            <tr>
                <td>
                    <span class="color-dot" style="background:{{ $p['color'] }}"></span>
                    {{ $p['name'] }}
                </td>
                <td>{{ $p['cash'] }} E</td>
                <td>{{ $p['houseCount'] }}</td>
                <td>
                    @if(count($p['powerplants']) > 0)
                        @foreach($p['powerplants'] as $pp)
                            @php
                                $fuelClass = $pp->card ? 'pp-'.$pp->card->fuel_type : 'pp-unknown';
                                $label = $pp->card
                                    ? '#'.$pp->card_number.' ('.$pp->card->cities.'🏙)'
                                    : '#'.$pp->card_number;
                            @endphp
                            <span class="pp-badge {{ $fuelClass }}">{{ $label }}</span>
                        @endforeach
                    @else
                        <span style="color:#555">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p style="color:#555">No players found.</p>
    @endif
</div>

@else
<div class="no-session">
    <p>The moderator has not started a game session yet.</p>
    <p style="margin-top:8px;">This page will auto-refresh when a session begins.</p>
</div>
@endif

<p class="refresh-note">Auto-refreshes every 10 seconds &nbsp;|&nbsp; Market for game {{ $moderatorId }}</p>

</body>
</html>
