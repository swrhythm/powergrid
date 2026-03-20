<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\player;
use App\Models\playerTransaction;
use App\Models\PowerplantCard;
use App\Models\PlayerPowerplant;
use App\Models\PlayerResource;
use Illuminate\Http\Request;
use Redirect;

class GameSessionController extends Controller
{
    // -------------------------------------------------------------------------
    // Helper: authenticate moderator from request
    // -------------------------------------------------------------------------
    private function authModerator(Request $request): ?player
    {
        $mod = player::where([
            ['id',       $request->input('moderatorId')],
            ['passCode', $request->input('moderatorPasscode')],
        ])->whereIn('type', ['ModOpen', 'ModClosed'])->first();

        return $mod;
    }

    // -------------------------------------------------------------------------
    // Helper: get or create game session for moderator
    // -------------------------------------------------------------------------
    private function getOrCreateSession(int $moderatorId, ?int $playerCount = null): GameSession
    {
        $session = GameSession::where('moderator_id', $moderatorId)->first();
        if (!$session) {
            $initial = GameSession::initialMarket();
            $session = GameSession::create([
                'moderator_id'   => $moderatorId,
                'step'           => 1,
                'player_count'   => $playerCount ?? 4,
                'coal_slots'    => $initial['coal_slots'],
                'oil_slots'     => $initial['oil_slots'],
                'garbage_slots' => $initial['garbage_slots'],
                'uranium_slots' => $initial['uranium_slots'],
            ]);

            // Create player_resources rows for all players under this mod
            $players = player::where('moderatorId', $moderatorId)->get();
            foreach ($players as $p) {
                PlayerResource::firstOrCreate([
                    'player_id'       => $p->id,
                    'game_session_id' => $session->id,
                ], ['coal' => 0, 'oil' => 0, 'garbage' => 0, 'uranium' => 0]);
            }
        }
        return $session;
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/setup  — create / update game session
    // -------------------------------------------------------------------------
    public function setup(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
            'player_count'      => 'required|integer|min:2|max:6',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        // Reset existing session if any
        GameSession::where('moderator_id', $mod->id)->delete();

        $initial = GameSession::initialMarket();
        $session = GameSession::create([
            'moderator_id'   => $mod->id,
            'step'           => 1,
            'player_count'   => $request->input('player_count'),
            'coal_slots'    => $initial['coal_slots'],
            'oil_slots'     => $initial['oil_slots'],
            'garbage_slots' => $initial['garbage_slots'],
            'uranium_slots' => $initial['uranium_slots'],
        ]);

        // Create player_resources for each player
        $players = player::where('moderatorId', $mod->id)->get();
        foreach ($players as $p) {
            PlayerResource::updateOrCreate(
                ['player_id' => $p->id, 'game_session_id' => $session->id],
                ['coal' => 0, 'oil' => 0, 'garbage' => 0, 'uranium' => 0]
            );
        }

        $modId = $mod->id;
        $modPc = $mod->passCode;
        return Redirect::to("/playerDetail?id=$modId&passcode=$modPc")
            ->with('success', 'Game session started!');
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/advanceStep  — move Step 1→2→3
    // -------------------------------------------------------------------------
    public function advanceStep(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $session = GameSession::where('moderator_id', $mod->id)->first();
        if (!$session) return Redirect::back()->withErrors(['No game session found. Start a game first.']);
        if ($session->step >= 3) return Redirect::back()->withErrors(['Already at Step 3']);

        $session->step++;
        $session->save();

        $modId = $mod->id;
        $modPc = $mod->passCode;
        return Redirect::to("/playerDetail?id=$modId&passcode=$modPc")
            ->with('success', "Advanced to Step {$session->step}");
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/buyResource  — buy resource from market for a player
    // -------------------------------------------------------------------------
    public function buyResource(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
            'playerId'          => 'required|numeric',
            'resource_type'     => 'required|in:coal,oil,garbage,uranium',
            'quantity'          => 'required|integer|min:1|max:16',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $targetPlayer = player::where('id', $request->input('playerId'))
            ->where('moderatorId', $mod->id)->first();
        if (!$targetPlayer) return Redirect::back()->withErrors(['Player not found']);

        $session = $this->getOrCreateSession($mod->id);
        $type = $request->input('resource_type');
        $qty  = (int) $request->input('quantity');

        // Check market supply
        if ($session->availableSupply($type) < $qty) {
            return Redirect::back()->withErrors(["Not enough $type in market (only " . $session->availableSupply($type) . " available)"]);
        }

        // Calculate cost
        $calc = $session->calcBuyCost($type, $qty);
        $cost = $calc['cost'];

        // Check player cash
        $playerCash = playerTransaction::where('playerId', $targetPlayer->id)->sum('total');
        if ($playerCash < $cost) {
            return Redirect::back()->withErrors(["Player doesn't have enough Elektro (needs $cost, has $playerCash)"]);
        }

        // Check storage capacity
        $resources = PlayerResource::firstOrCreate(
            ['player_id' => $targetPlayer->id, 'game_session_id' => $session->id],
            ['coal' => 0, 'oil' => 0, 'garbage' => 0, 'uranium' => 0]
        );
        $maxStorage = PlayerResource::calcMaxStorage($targetPlayer->id, $session->id);

        if ($maxStorage[$type] <= 0) {
            return Redirect::back()->withErrors(["Player has no powerplant that accepts $type"]);
        }
        if (($resources->$type + $qty) > $maxStorage[$type]) {
            $canStore = $maxStorage[$type] - $resources->$type;
            return Redirect::back()->withErrors(["Storage full! Player can only store $canStore more $type (max {$maxStorage[$type]})"]);
        }

        // Deduct from market
        $session->deductResource($type, $qty);
        $session->save();

        // Deduct Elektro from player
        $tx = new playerTransaction();
        $tx->playerId    = $targetPlayer->id;
        $tx->total       = -$cost;
        $tx->description = "[Buy $qty $type @ market] {$calc['breakdown']}";
        $tx->save();

        // Add to player resources
        $resources->$type += $qty;
        $resources->save();

        $modId = $mod->id;
        $modPc = $mod->passCode;
        return Redirect::to("/playerDetail?id=$modId&passcode=$modPc");
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/refillMarket  — end-of-round resource replenishment
    // -------------------------------------------------------------------------
    public function refillMarket(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $session = $this->getOrCreateSession($mod->id);
        $session->refillMarket();
        $session->save();

        $modId = $mod->id;
        $modPc = $mod->passCode;
        return Redirect::to("/playerDetail?id=$modId&passcode=$modPc")
            ->with('success', 'Market refilled for end of round.');
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/addPowerplant  — record a player buying a powerplant
    // -------------------------------------------------------------------------
    public function addPowerplant(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
            'playerId'          => 'required|numeric',
            'card_number'       => 'required|integer|min:1',
            'pp_cost'           => 'required|integer|min:0',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $targetPlayer = player::where('id', $request->input('playerId'))
            ->where('moderatorId', $mod->id)->first();
        if (!$targetPlayer) return Redirect::back()->withErrors(['Player not found']);

        $session = $this->getOrCreateSession($mod->id);
        $cardNumber = (int) $request->input('card_number');
        $cost = (int) $request->input('pp_cost');

        // Check player cash
        $playerCash = playerTransaction::where('playerId', $targetPlayer->id)->sum('total');
        if ($playerCash < $cost) {
            return Redirect::back()->withErrors(["Not enough Elektro (needs $cost, has $playerCash)"]);
        }

        // Check player doesn't already own this card
        $existing = PlayerPowerplant::where([
            'player_id'       => $targetPlayer->id,
            'game_session_id' => $session->id,
            'card_number'     => $cardNumber,
        ])->first();
        if ($existing) return Redirect::back()->withErrors(["Player already owns powerplant #$cardNumber"]);

        // Check max 3 powerplants
        $ownedCount = PlayerPowerplant::where([
            'player_id'       => $targetPlayer->id,
            'game_session_id' => $session->id,
        ])->count();
        if ($ownedCount >= 3) {
            return Redirect::back()->withErrors(['Player already has 3 powerplants. Use Replace to swap one.']);
        }

        // Create record
        PlayerPowerplant::create([
            'player_id'       => $targetPlayer->id,
            'game_session_id' => $session->id,
            'card_number'     => $cardNumber,
        ]);

        // Ensure player_resources row exists
        PlayerResource::firstOrCreate(
            ['player_id' => $targetPlayer->id, 'game_session_id' => $session->id],
            ['coal' => 0, 'oil' => 0, 'garbage' => 0, 'uranium' => 0]
        );

        // Deduct Elektro + update largestPowerplant
        if ($cost > 0) {
            $card = PowerplantCard::find($cardNumber);
            $tx = new playerTransaction();
            $tx->playerId    = $targetPlayer->id;
            $tx->total       = -$cost;
            $tx->description = "[Buy Powerplant <$cardNumber>]";
            $tx->save();
        }

        if ($targetPlayer->largestPowerplant < $cardNumber) {
            $targetPlayer->largestPowerplant = $cardNumber;
            $targetPlayer->save();
        }

        $modId = $mod->id;
        $modPc = $mod->passCode;
        return Redirect::to("/playerDetail?id=$modId&passcode=$modPc");
    }

    // -------------------------------------------------------------------------
    // POST /gameSession/replacePowerplant  — swap an old PP for a new one
    // -------------------------------------------------------------------------
    public function replacePowerplant(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
            'playerId'          => 'required|numeric',
            'old_card_number'   => 'required|integer|min:1',
            'new_card_number'   => 'required|integer|min:1',
            'pp_cost'           => 'required|integer|min:0',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = $this->authModerator($request);
        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $targetPlayer = player::where('id', $request->input('playerId'))
            ->where('moderatorId', $mod->id)->first();
        if (!$targetPlayer) return Redirect::back()->withErrors(['Player not found']);

        $session = $this->getOrCreateSession($mod->id);
        $oldCard = (int) $request->input('old_card_number');
        $newCard = (int) $request->input('new_card_number');
        $cost    = (int) $request->input('pp_cost');

        // Verify old card is owned
        $oldPP = PlayerPowerplant::where([
            'player_id'       => $targetPlayer->id,
            'game_session_id' => $session->id,
            'card_number'     => $oldCard,
        ])->first();
        if (!$oldPP) return Redirect::back()->withErrors(["Player doesn't own powerplant #$oldCard"]);

        // Check player cash
        $playerCash = playerTransaction::where('playerId', $targetPlayer->id)->sum('total');
        if ($playerCash < $cost) {
            return Redirect::back()->withErrors(["Not enough Elektro (needs $cost, has $playerCash)"]);
        }

        // Remove old, add new
        $oldPP->delete();
        PlayerPowerplant::create([
            'player_id'       => $targetPlayer->id,
            'game_session_id' => $session->id,
            'card_number'     => $newCard,
        ]);

        // Warn if resources now exceed new capacity (but don't auto-deduct)
        $maxStorage = PlayerResource::calcMaxStorage($targetPlayer->id, $session->id);
        $resources  = PlayerResource::where([
            'player_id' => $targetPlayer->id, 'game_session_id' => $session->id
        ])->first();
        $warnings = [];
        if ($resources) {
            foreach (['coal', 'oil', 'garbage', 'uranium'] as $t) {
                if ($resources->$t > $maxStorage[$t]) {
                    $warnings[] = "Warning: player has {$resources->$t} $t but new capacity is only {$maxStorage[$t]}. Manually discard excess.";
                }
            }
        }

        // Deduct Elektro
        if ($cost > 0) {
            $tx = new playerTransaction();
            $tx->playerId    = $targetPlayer->id;
            $tx->total       = -$cost;
            $tx->description = "[Buy Powerplant <$newCard>] (replaced #$oldCard)";
            $tx->save();
        }

        // Update largestPowerplant
        if ($targetPlayer->largestPowerplant < $newCard) {
            $targetPlayer->largestPowerplant = $newCard;
            $targetPlayer->save();
        }

        $modId = $mod->id;
        $modPc = $mod->passCode;
        $redirect = Redirect::to("/playerDetail?id=$modId&passcode=$modPc");
        foreach ($warnings as $w) {
            $redirect = $redirect->withErrors([$w]);
        }
        return $redirect;
    }

    // -------------------------------------------------------------------------
    // GET /market/{moderatorId}  — public TV/big-screen market view (no auth)
    // -------------------------------------------------------------------------
    public function marketView(int $moderatorId)
    {
        $mod = player::whereIn('type', ['ModOpen', 'ModClosed'])
            ->where('id', $moderatorId)->first();
        if (!$mod) abort(404);

        $session = GameSession::where('moderator_id', $moderatorId)->first();

        $playerList = [];
        if ($session) {
            $rawPlayers = player::where('moderatorId', $moderatorId)
                ->orderBy('houseCount', 'desc')
                ->orderBy('largestPowerplant', 'desc')
                ->get();

            foreach ($rawPlayers as $p) {
                $cash = playerTransaction::where('playerId', $p->id)->sum('total');
                $pps  = PlayerPowerplant::where([
                    'player_id'       => $p->id,
                    'game_session_id' => $session->id,
                ])->with('card')->get();

                $playerList[] = [
                    'name'        => $p->name,
                    'color'       => $p->color,
                    'cash'        => $cash,
                    'houseCount'  => $p->houseCount,
                    'powerplants' => $pps,
                ];
            }
        }

        return view('marketView', [
            'session'    => $session,
            'playerList' => $playerList,
            'moderatorId'=> $moderatorId,
        ]);
    }
}
