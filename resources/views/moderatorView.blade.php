<!DOCTYPE html>
<html lang="en">
<head>
    <title>Moderator View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .resource-slot { display:inline-block; width:28px; height:28px; border:1px solid #888; text-align:center; line-height:26px; font-size:11px; margin:1px; border-radius:3px; }
        .slot-full    { background:#555; color:#fff; }
        .slot-partial { background:#aaa; color:#000; }
        .slot-empty   { background:#eee; color:#999; }
        .slot-unavail { background:#f5f5f5; color:#ccc; border-color:#ddd; }
        .resource-row { margin-bottom:8px; }
        .resource-label { display:inline-block; width:70px; font-weight:bold; font-size:12px; vertical-align:middle; }
        .price-tag { font-size:10px; color:#666; display:inline-block; width:28px; text-align:center; margin:1px; }
        .pp-badge { display:inline-block; padding:2px 6px; margin:1px; border-radius:3px; font-size:11px; border:1px solid #aaa; }
        .pp-coal    { background:#8B4513; color:#fff; }
        .pp-oil     { background:#333; color:#fff; }
        .pp-garbage { background:#DAA520; color:#000; }
        .pp-uranium { background:#cc0000; color:#fff; }
        .pp-hybrid  { background:#556; color:#fff; }
        .pp-eco     { background:#228B22; color:#fff; }
        .pp-unknown { background:#888; color:#fff; }
        .section-card { border:1px solid #dee2e6; border-radius:4px; padding:12px; margin-bottom:12px; }
        .step-badge { font-size:1.1rem; font-weight:bold; padding:4px 12px; border-radius:4px; }
    </style>
</head>
<body>
<div class="container-fluid py-2">

    {{-- ===== ERROR MESSAGES ===== --}}
    @if($errors->any())
        <div class="row">
            <div class="col-12">
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger alert-dismissible fade show py-1 mb-1">
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                        {{$error}}
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row justify-content-center mb-2">
        <h4 class="mb-0">Moderator View
            @if($gameSession)
                &nbsp;<span class="step-badge badge badge-{{ $gameSession->step == 1 ? 'secondary' : ($gameSession->step == 2 ? 'warning' : 'danger') }}">
                    Step {{ $gameSession->step }}
                </span>
                &nbsp;<small class="text-muted">{{ $gameSession->player_count }} players</small>
                &nbsp;<a href="/market/{{$moderatorId}}" target="_blank" class="btn btn-sm btn-outline-info">📺 Public Market View</a>
            @endif
        </h4>
    </div>

    <div class="row">

        {{-- ===== LEFT COLUMN: Transaction + Player Stats ===== --}}
        <div class="col-md-4 col-sm-12 p-2">

            {{-- Game Session Setup --}}
            <div class="section-card bg-light">
                @if(!$gameSession)
                    <h5>Start Game Session</h5>
                    <form action="/gameSession/setup" method="post" class="form-inline">
                        @csrf
                        <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                        <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                        <label class="mr-2">Players:</label>
                        <select name="player_count" class="form-control form-control-sm mr-2">
                            @foreach([2,3,4,5,6] as $n)
                                <option value="{{$n}}" {{ $n==4 ? 'selected' : '' }}>{{$n}}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-success">Start Game</button>
                    </form>
                @else
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong>Step {{ $gameSession->step }}</strong> of 3 &nbsp;
                            <small class="text-muted">{{ $gameSession->player_count }} players</small>
                        </div>
                        <div>
                            @if($gameSession->step < 3)
                                <form action="/gameSession/advanceStep" method="post" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Advance to Step {{ $gameSession->step + 1 }}?')">
                                        → Step {{ $gameSession->step + 1 }}
                                    </button>
                                </form>
                            @endif
                            <form action="/gameSession/refillMarket" method="post" class="d-inline">
                                @csrf
                                <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                                <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                                @php
                                    $refill   = $gameSession->getRefillAmounts();
                                    $rCoal    = $refill['coal'];
                                    $rOil     = $refill['oil'];
                                    $rGarbage = $refill['garbage'];
                                    $rUranium = $refill['uranium'];
                                @endphp
                                <button type="submit" class="btn btn-sm btn-outline-secondary"
                                    title="+{{ $rCoal }} coal, +{{ $rOil }} oil, +{{ $rGarbage }} garbage, +{{ $rUranium }} uranium"
                                    onclick="return confirm('Refill market?\n+{{ $rCoal }} coal\n+{{ $rOil }} oil\n+{{ $rGarbage }} garbage\n+{{ $rUranium }} uranium')">
                                    ↺ Refill Market
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- Reset option --}}
                    <div class="mt-1">
                        <small>
                            <a href="#resetModal" data-toggle="modal" class="text-danger">Reset game session</a>
                        </small>
                    </div>
                @endif
            </div>

            {{-- Manual Transaction Form --}}
            <div class="section-card">
                <h5>Manual Transaction</h5>
                <form action="/inputPlayerTransaction" method="post">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Player:</label>
                        @foreach($playerList as $player)
                            <div class="form-check py-0">
                                <label class="form-check-label small" style="background-color:{{$player->color}};padding:3px 3px 3px 20px;width:100%;border-radius:3px">
                                    <input type="radio" name="playerId" class="form-check-input" value="{{$player->id}}" required>
                                    {{$player->name}}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Amount:</label>
                        <input type="number" class="form-control form-control-sm" id="total" name="total" value="0">
                    </div>
                    <div class="form-group mb-1">
                        <label class="mb-0 small">Description:</label>
                        <input type="text" class="form-control form-control-sm" id="description" name="description">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                    <button type="reset" class="btn btn-sm btn-danger">Reset</button>
                </form>
            </div>

            {{-- Player Stats Table --}}
            @if($moderatorType == "ModOpen")
            <div class="section-card">
                <h5>Current Stats</h5>
                <div class="table-responsive">
                <table class="table table-sm table-striped mb-0" style="font-size:12px">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Cash</th>
                            <th>🏠</th>
                            <th>Powerplants</th>
                            @if($gameSession)
                            <th>Resources</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($playerList as $player)
                        @php
                            $totalElektron = \App\Models\playerTransaction::where('playerId',$player->id)->sum('total');
                            $pps  = $playerPowerplants[$player->id] ?? collect();
                            $res  = $playerResources[$player->id] ?? null;
                            $maxS = $playerMaxStorage[$player->id] ?? [];
                        @endphp
                        <tr>
                            <td onclick="alert('ID:{{$player->id}} Pass:{{$player->passCode}}')">{{$player->id}}</td>
                            <td style="background-color:{{$player->color}};color:{{ in_array(strtolower($player->color),['black','#000','#000000','navy','darkblue','darkgreen']) ? '#fff' : '#000' }}">
                                {{$player->name}}
                            </td>
                            <td>{{$totalElektron}}</td>
                            <td>{{$player->houseCount}}</td>
                            <td>
                                @if($pps->count() > 0)
                                    @foreach($pps as $pp)
                                        @php
                                            $fuelClass = $pp->card ? 'pp-'.$pp->card->fuel_type : 'pp-unknown';
                                            $label = $pp->card
                                                ? '#'.$pp->card_number.' ('.$pp->card->cities.'🏙)'
                                                : '#'.$pp->card_number;
                                        @endphp
                                        <span class="pp-badge {{$fuelClass}}" title="{{ $pp->card ? $pp->card->fuelLabel().' / needs '.$pp->card->fuel_needed.' / powers '.$pp->card->cities.' cities' : 'Unknown card' }}">{{$label}}</span>
                                    @endforeach
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                            @if($gameSession)
                            <td>
                                @if($res)
                                    @foreach(['coal','oil','garbage','uranium'] as $rt)
                                        @if(($maxS[$rt] ?? 0) > 0)
                                            <span title="{{$rt}}">
                                                {{ $rt == 'coal' ? '🪨' : ($rt == 'oil' ? '🛢' : ($rt == 'garbage' ? '🗑' : '☢')) }}
                                                {{$res->$rt}}/{{$maxS[$rt]}}
                                            </span><br>
                                        @endif
                                    @endforeach
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            </div>
            @endif

        </div>{{-- end left column --}}

        {{-- ===== RIGHT COLUMN: Market, Expenses, Income ===== --}}
        <div class="col-md-8 col-sm-12 p-2">

            {{-- ===== SETUP CHECKLIST (shown before game session starts) ===== --}}
            @if(!$gameSession)
            <div class="section-card border-warning" style="background:#fffbe6">
                <h5 class="text-warning">⚙ Game Setup Checklist</h5>
                <p class="small text-muted mb-2">Complete these steps before starting the game:</p>
                <ul class="list-unstyled mb-0" style="font-size:14px">
                    <li>✅ &nbsp;<strong>Players created</strong> — {{ count($playerList) }} players in this session (+50E each)</li>
                    <li class="mt-1">🔲 &nbsp;<strong>Initial powerplant auction</strong> — Do the physical auction, then use <em>Add PP</em> below to record each player's card</li>
                    <li class="mt-1">🔲 &nbsp;<strong>Start game session</strong> — Select player count and click <em>Start Game</em> on the left when the board is ready</li>
                </ul>
            </div>
            @endif

            {{-- ===== RESOURCE MARKET BOARD ===== --}}
            @if($gameSession)
            <div class="section-card border-info">
                <h5 class="text-info">Resource Market</h5>

                @php
                    $resourceConfig = [
                        'coal'    => ['label' => '🪨 Coal',    'slots' => $gameSession->coal_slots,    'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 0, 'color' => '#8B4513'],
                        'oil'     => ['label' => '🛢 Oil',     'slots' => $gameSession->oil_slots,     'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 2, 'color' => '#333'],
                        'garbage' => ['label' => '🗑 Garbage', 'slots' => $gameSession->garbage_slots, 'maxSlot' => 8,  'maxPer' => 3, 'minIdx' => 6, 'color' => '#DAA520'],
                        'uranium' => ['label' => '☢ Uranium',  'slots' => $gameSession->uranium_slots, 'maxSlot' => 16, 'maxPer' => 1, 'minIdx' => 0, 'color' => '#cc0000'],
                    ];
                @endphp

                @foreach($resourceConfig as $type => $cfg)
                    <div class="resource-row">
                        <span class="resource-label" style="color:{{ $cfg['color'] }}">{{ $cfg['label'] }}</span>
                        @for($i = 0; $i < $cfg['maxSlot']; $i++)
                            @if($i < $cfg['minIdx'])
                                <span class="resource-slot slot-unavail" title="Slot {{ $i+1 }}: N/A">—</span>
                            @else
                                @php
                                    $count = $cfg['slots'][$i] ?? 0;
                                    $slotClass = $count == $cfg['maxPer'] ? 'slot-full' : ($count > 0 ? 'slot-partial' : 'slot-empty');
                                @endphp
                                <span class="resource-slot {{$slotClass}}"
                                    title="Slot {{ $i+1 }}: {{ $count }}/{{ $cfg['maxPer'] }} | Price: {{ $i+1 }}E">
                                    {{ $count > 0 ? $count : '·' }}
                                </span>
                            @endif
                        @endfor
                        <br>
                        <span class="resource-label"></span>
                        @for($i = 0; $i < $cfg['maxSlot']; $i++)
                            <span class="price-tag" style="{{ $i < $cfg['minIdx'] ? 'color:transparent' : '' }}">{{ $i+1 }}E</span>
                        @endfor
                    </div>
                @endforeach

                {{-- Buy Resource from Market --}}
                <hr class="my-2">
                <h6>Buy Resource from Market</h6>
                <form action="/gameSession/buyResource" method="post">
                    @csrf
                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                    <div class="form-row align-items-end">
                        <div class="col-auto">
                            <label class="small mb-0">Player</label>
                            <select name="playerId" class="form-control form-control-sm" required>
                                <option value="">Select...</option>
                                @foreach($playerList as $player)
                                    <option value="{{$player->id}}">{{$player->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="small mb-0">Resource</label>
                            <select name="resource_type" class="form-control form-control-sm" required>
                                <option value="coal">🪨 Coal (cheapest: {{ $gameSession->cheapestPrice('coal') ?? '—' }}E)</option>
                                <option value="oil">🛢 Oil (cheapest: {{ $gameSession->cheapestPrice('oil') ?? '—' }}E)</option>
                                <option value="garbage">🗑 Garbage (cheapest: {{ $gameSession->cheapestPrice('garbage') ?? '—' }}E)</option>
                                <option value="uranium">☢ Uranium (cheapest: {{ $gameSession->cheapestPrice('uranium') ?? '—' }}E)</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="small mb-0">Qty</label>
                            <input type="number" name="quantity" class="form-control form-control-sm" style="width:60px" min="1" max="16" value="1" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-danger">Buy from Market</button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            {{-- ===== EXPENSES ===== --}}
            <div class="section-card border-danger">
                <h5 class="text-danger">Expenses</h5>

                {{-- Powerplant Section --}}
                <h6>Powerplant</h6>
                @if($gameSession)
                {{-- Add Powerplant --}}
                <form action="/gameSession/addPowerplant" method="post" class="form-row align-items-end mb-2">
                    @csrf
                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                    <div class="col-auto">
                        <label class="small mb-0">Player</label>
                        <select name="playerId" class="form-control form-control-sm" required>
                            <option value="">Select...</option>
                            @foreach($playerList as $player)
                                <option value="{{$player->id}}">{{$player->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="small mb-0">Card #</label>
                        <input type="number" name="card_number" class="form-control form-control-sm" style="width:70px" min="1" placeholder="e.g. 12" required>
                    </div>
                    <div class="col-auto">
                        <label class="small mb-0">Cost (Elek)</label>
                        <input type="number" name="pp_cost" class="form-control form-control-sm" style="width:70px" min="0" placeholder="e.g. 14" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-danger">+ Add PP</button>
                    </div>
                </form>

                {{-- Replace Powerplant --}}
                <form action="/gameSession/replacePowerplant" method="post" class="form-row align-items-end mb-2">
                    @csrf
                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                    <div class="col-auto">
                        <label class="small mb-0">Player</label>
                        <select name="playerId" class="form-control form-control-sm" required>
                            <option value="">Select...</option>
                            @foreach($playerList as $player)
                                <option value="{{$player->id}}">{{$player->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="small mb-0">Old #</label>
                        <input type="number" name="old_card_number" class="form-control form-control-sm" style="width:65px" min="1" placeholder="old" required>
                    </div>
                    <div class="col-auto">
                        <label class="small mb-0">New #</label>
                        <input type="number" name="new_card_number" class="form-control form-control-sm" style="width:65px" min="1" placeholder="new" required>
                    </div>
                    <div class="col-auto">
                        <label class="small mb-0">Cost (Elek)</label>
                        <input type="number" name="pp_cost" class="form-control form-control-sm" style="width:70px" min="0" placeholder="e.g. 20" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-outline-warning">↔ Replace PP</button>
                    </div>
                </form>
                @else
                {{-- No game session: old-style manual powerplant --}}
                <div class="form-row mb-2">
                    <div class="col-auto">
                        Number: <input style="width:50px;height:26px;padding:0px" type="number" id="powerplantNumber">
                    </div>
                    <div class="col-auto">
                        Value: <input style="width:50px;height:26px;padding:0px" type="number" id="powerplantValue">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-danger" onclick="buyPowerplant()">Buy Powerplant</button>
                    </div>
                </div>
                @endif

                <hr class="my-2">

                {{-- Resource buying (manual / fallback when no session) --}}
                @if(!$gameSession)
                <h6>Resource (manual)</h6>
                <div class="form-row mb-2">
                    @foreach(['Coal'=>'chocolate','Oil'=>'#333','Trash'=>'#DAA520','Nuclear'=>'#cc0000'] as $res => $col)
                        <div class="form-check mr-1">
                            <label class="form-check-label text-light small" style="background-color:{{$col}};padding:4px 8px 4px 22px;border-radius:3px;">
                                <input type="radio" name="resourceType" class="form-check-input" value="{{$res}}">{{$res}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="mb-2">
                    @for($i=1;$i<=16;$i++)
                        <button class="btn btn-sm btn-outline-danger mb-1" onclick="buyResource({{$i}},'[Buy {{$i}} ')">{{$i}}</button>
                    @endfor
                </div>
                @endif

                {{-- Houses --}}
                <h6>Rumah (Houses)</h6>
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-danger" onclick="buyHouse(-10,'[Rumah Step 1] ')">Step 1 — 10E</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="buyHouse(-15,'[Rumah Step 2] ')">Step 2 — 15E</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="buyHouse(-20,'[Rumah Step 3] ')">Step 3 — 20E</button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="buyHouse(0,'[Subtract 1 Rumah] ')">- Subtract</button>
                </div>

                {{-- Connector --}}
                <h6>Connection</h6>
                <div class="form-row">
                    <div class="col-auto">
                        <input type="number" id="conectorValue" class="form-control form-control-sm" style="width:80px" placeholder="cost">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-danger" onclick="payConnector()">Pay Connector</button>
                    </div>
                </div>
            </div>

            {{-- ===== INCOME ===== --}}
            <div class="section-card border-success">
                <h5 class="text-success">Income — Houses Lit</h5>
                <div class="mb-2">
                    @php
                        $incomeTable = [
                            0=>10, 1=>22, 2=>33, 3=>44, 4=>54, 5=>64, 6=>73, 7=>82, 8=>90, 9=>98,
                            10=>105, 11=>112, 12=>118, 13=>124, 14=>129, 15=>134, 16=>138, 17=>142, 18=>145, 19=>148, 20=>150
                        ];
                    @endphp
                    @foreach($incomeTable as $n => $pay)
                        <button class="btn btn-sm btn-success mb-1" onclick="income({{$pay}},'[{{$n}} house turned on] ')">{{$n}}</button>
                    @endforeach
                </div>
                <h6>Other</h6>
                <div class="mb-2">
                    <button class="btn btn-sm btn-success" onclick="income(50,'[Starting Money] ')">Starting Money (+50)</button>
                </div>
            </div>

            {{-- ===== LAST TRANSACTIONS ===== --}}
            <div class="section-card border-success">
                <h5>Last Transactions</h5>
                <table class="table table-sm table-bordered mb-0" style="font-size:12px">
                    <thead>
                        <tr><th>Player</th><th>Amount</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        @foreach($lastTransaction as $tx)
                        <tr>
                            <td style="color:{{$tx->color}}">{{$tx->name}}</td>
                            <td class="{{ $tx->total < 0 ? 'text-danger' : 'text-success' }}">{{$tx->total}}</td>
                            <td>{{$tx->description}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>{{-- end right column --}}
    </div>{{-- end row --}}
</div>{{-- end container --}}

{{-- Reset Modal --}}
<div class="modal fade" id="resetModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Reset Game Session</h5></div>
            <div class="modal-body">This will reset the market to its initial state. Player money and houses are unchanged.</div>
            <div class="modal-footer">
                <form action="/gameSession/setup" method="post">
                    @csrf
                    <input type="hidden" name="moderatorId" value="{{$moderatorId}}">
                    <input type="hidden" name="moderatorPasscode" value="{{$moderatorPasscode}}">
                    <input type="hidden" name="player_count" value="{{ $gameSession ? $gameSession->player_count : 4 }}">
                    <button type="submit" class="btn btn-danger btn-sm">Reset</button>
                </form>
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    function buyHouse(total, desc) {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) + parseInt(total);
        document.getElementById("description").value = document.getElementById("description").value + desc;
    }
    function buyPowerplant() {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(document.getElementById("powerplantValue").value);
        document.getElementById("description").value = document.getElementById("description").value + "[Buy Powerplant <" + document.getElementById("powerplantNumber").value + ">]";
    }
    function buyResource(price, desc) {
        var radioValue = $("input[name='resourceType']:checked").val();
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(price);
        document.getElementById("description").value = document.getElementById("description").value + desc + radioValue + "]";
    }
    function payConnector() {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) - parseInt(document.getElementById("conectorValue").value);
        document.getElementById("description").value = document.getElementById("description").value + "[Pay Connector " + parseInt(document.getElementById("conectorValue").value) + "]";
    }
    function income(value, desc) {
        document.getElementById("total").value = parseInt(document.getElementById("total").value) + value;
        document.getElementById("description").value = document.getElementById("description").value + desc;
    }
</script>
</body>
</html>
