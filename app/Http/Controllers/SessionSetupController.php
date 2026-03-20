<?php

namespace App\Http\Controllers;

use App\Models\player;
use App\Models\playerTransaction;
use App\Models\PlayerResource;
use Illuminate\Http\Request;
use Redirect;

class SessionSetupController extends Controller
{
    // -------------------------------------------------------------------------
    // GET /createSession — show new-session form
    // -------------------------------------------------------------------------
    public function createSessionForm()
    {
        return view('createSession');
    }

    // -------------------------------------------------------------------------
    // POST /createSession — create moderator, redirect to show ID
    // -------------------------------------------------------------------------
    public function createSession(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'passcode' => 'required|min:4',
        ]);
        if ($validator->fails()) {
            return Redirect::to('/createSession')->withInput()->withErrors($validator);
        }

        $mod = new player();
        $mod->name        = 'Moderator';
        $mod->color       = '#888888';
        $mod->type        = 'ModOpen';
        $mod->passCode    = $request->input('passcode');
        $mod->moderatorId = 0;
        $mod->houseCount  = 0;
        $mod->save();

        return redirect("/sessionCreated?id={$mod->id}&passcode=" . urlencode($mod->passCode));
    }

    // -------------------------------------------------------------------------
    // GET /sessionCreated — show assigned ID
    // -------------------------------------------------------------------------
    public function sessionCreated(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'       => 'required|numeric',
            'passcode' => 'required',
        ]);
        if ($validator->fails()) return redirect('/');

        $mod = player::where([
            ['id',       $request->input('id')],
            ['passCode', $request->input('passcode')],
        ])->whereIn('type', ['ModOpen', 'ModClosed'])->first();

        if (!$mod) return redirect('/');

        return view('sessionCreated', [
            'mod' => $mod,
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /setupPlayers — show player-setup wizard
    // -------------------------------------------------------------------------
    public function setupPlayersForm(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id'       => 'required|numeric',
            'passcode' => 'required',
        ]);
        if ($validator->fails()) return Redirect::to('/viewPlayer')->withErrors($validator);

        $mod = player::where([
            ['id',       $request->input('id')],
            ['passCode', $request->input('passcode')],
        ])->whereIn('type', ['ModOpen', 'ModClosed'])->first();

        if (!$mod) return Redirect::to('/viewPlayer')->withErrors(['Invalid moderator credentials']);

        return view('setupPlayers', [
            'moderatorId'       => $mod->id,
            'moderatorPasscode' => $mod->passCode,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /setupPlayers — create all players with default "1234" password
    // -------------------------------------------------------------------------
    public function setupPlayers(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'moderatorId'       => 'required|numeric',
            'moderatorPasscode' => 'required',
            'player_count'      => 'required|integer|min:2|max:6',
        ]);
        if ($validator->fails()) return Redirect::back()->withErrors($validator);

        $mod = player::where([
            ['id',       $request->input('moderatorId')],
            ['passCode', $request->input('moderatorPasscode')],
        ])->whereIn('type', ['ModOpen', 'ModClosed'])->first();

        if (!$mod) return Redirect::back()->withErrors(['Invalid moderator credentials']);

        $count = (int) $request->input('player_count');
        $names  = $request->input('name', []);
        $colors = $request->input('color', []);

        // Validate each player row
        $errors = [];
        for ($i = 0; $i < $count; $i++) {
            if (empty($names[$i])) $errors[] = "Player " . ($i + 1) . " name is required.";
            if (empty($colors[$i])) $errors[] = "Player " . ($i + 1) . " color is required.";
        }
        if (!empty($errors)) return Redirect::back()->withErrors($errors);

        $createdPlayers = [];
        for ($i = 0; $i < $count; $i++) {
            $p = new player();
            $p->name        = trim($names[$i]);
            $p->color       = $colors[$i];
            $p->type        = 'Player';
            $p->passCode    = '1234';
            $p->moderatorId = $mod->id;
            $p->houseCount  = 0;
            $p->save();

            // Give starting money of 50E
            $tx = new playerTransaction();
            $tx->playerId    = $p->id;
            $tx->total       = 50;
            $tx->description = '[Starting Money]';
            $tx->save();

            $createdPlayers[] = $p;
        }

        return view('playersCreated', [
            'players'           => $createdPlayers,
            'moderatorId'       => $mod->id,
            'moderatorPasscode' => $mod->passCode,
        ]);
    }
}
