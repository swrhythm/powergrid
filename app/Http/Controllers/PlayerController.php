<?php

namespace App\Http\Controllers;

use App\Models\player;
use App\Models\playerTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Redirect;
use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function addPlayer()
    {
        return view('addPlayer');
    }
    /**
     * Display a listing of the resource.
     */
    public function addPlayerProcess(Request $request)
    {
        // Validation
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'color' => 'required',
                'type' => 'required|string',
                'passcode' => 'required'
            ]
        );

        if ($validator->fails()) {
            return Redirect::to('/addPlayer')->withInput()->withErrors($validator);
        }
        if ($request->input('type') == "Player" && is_null($request->input('moderatorId')) ) {
            return Redirect::to('/addPlayer')->withInput()->withErrors(["Player must input moderator Id"]);
        }

        //Process
        $player = new player();
        $player->name = $request->input('name');
        $player->color = $request->input('color');
        $player->type = $request->input('type');
        $player->passcode = $request->input('passcode');
        $player->houseCount = 0;
        if(in_array($request->input('type'),["ModOpen","ModClosed"])) {
            $player->moderatorId = 0;
        } else {
            $check = player::where('id',$request->input('moderatorId'))->first();
            if(is_Null($check) || !in_array($check->type,["ModOpen","ModClosed"])){
                return Redirect::to('/addPlayer')->withInput()->withErrors(["Moderator Id Wrong"]);
            }
            $player->moderatorId = $request->input('moderatorId');
        }

        if($player->save()){
            return redirect('/')->withErrors(["Player Created (ID:$player->id)"]);
        } else {
            return redirect('/')->withErrors(["Player Creation Failed"]);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function viewPlayer()
    {
        return view('viewPlayer');
    }
    /**
     * Display a listing of the resource.
     */
    public function playerDetails(Request $request)
    {
        // Validation
        $validator = \Validator::make(
            $request->all(),
            [
                'id' => 'required|numeric',
                'passcode' => 'required'
            ]
        );

        if ($validator->fails()) {
            return Redirect::to('/viewPlayer')->withInput()->withErrors($validator);
        }

        //Process
        $player = player::where([
            ['id', $request->input('id')],
            ['passCode', $request->input('passcode')]
        ])->first();
        if (is_null($player)) {
            return Redirect::to('/viewPlayer')->withInput()->withErrors(["Player not found"]);
        }
        if (in_array($player->type,["ModOpen","ModClosed"])) {
            $playerList = player::where('moderatorId',$player->id)->orderBy('houseCount','desc')->orderBy('largestPowerplant','desc')->get();
            $lastTransaction = DB::table('player_transactions')
                                    ->join('players', 'player_transactions.playerId', '=', 'players.id')
                                    ->select('players.name', 'players.color', 'player_transactions.total', 'player_transactions.description')
                                    ->where('players.moderatorId', $request->input('id'))
                                    ->orderBy('player_transactions.created_at', 'desc')
                                    ->limit(10)
                                    ->get();
            return view(
                'moderatorView',
                [
                    'playerList' => $playerList,
                    'moderatorId'=>$player->id,
                    'moderatorPasscode'=>$player->passCode,
                    'moderatorType'=>$player->type,
                    'lastTransaction'=>$lastTransaction
                ]
            );
        } else {
            $transaction = playerTransaction::where('playerId', $player->id)->orderBy('id', 'desc')->take(10)->get();
            $total = playerTransaction::where('playerId', $player->id)->sum('total');

            return view('playerDetail', ['player' => $player, 'transaction' => $transaction, 'total' => $total]);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function inputPlayerTransaction(Request $request)
    {
        // Validation
        $validator = \Validator::make(
            $request->all(),
            [
                'moderatorId' => 'required|numeric',
                'moderatorPasscode' => 'required',
                'playerId' =>'required|numeric',
                'total' =>'required|numeric',
            ]
        );

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        //Check
        $currentCash = playerTransaction::where('playerId',$request->input('playerId'))->sum('total');
        if($currentCash<$request->input('total')) {
            if ($validator->fails()) {
                return Redirect::back()->withErrors(["Player Elektron Not enough!!!"]);
            }
        }
        //Process
        $playerTransaction = new playerTransaction();
        $playerTransaction->playerId = $request->input('playerId');
        $playerTransaction->total = $request->input('total');
        $playerTransaction->description = $request->input('description');

        $playerTransaction->save();
        $newHouse = substr_count($request->input('description'), '[Rumah Step');
        if($newHouse > 0) {
            $player = player::where('id',$playerTransaction->playerId)->first();
            $player->houseCount = $player->houseCount + $newHouse;
            $player->save();
        }
        $substractHouse = substr_count($request->input('description'), '[Subtract 1 Rumah]');
        if($substractHouse > 0) {
            $player = player::where('id',$playerTransaction->playerId)->first();
            $player->houseCount = $player->houseCount - $substractHouse;
            $player->save();
        }
        
        if (strpos($request->input('description'), '[Buy Powerplant <') === 0) {
            $desc = $request->input('description');
            $desc = substr($desc, 17);
            $desc = substr($desc, 0, -2);
            $player = player::where('id',$playerTransaction->playerId)->first();
            if($player->largestPowerplant < $desc) {
                $player->largestPowerplant = $desc;
                $player->save();
            }
         }
        $powerPlantNumber = substr_count($request->input('description'), '[Rumah Step');
        $moderatorId = $request->input('moderatorId');
        $moderatorPasscode = $request->input('moderatorPasscode');
        return Redirect::to("/playerDetail?id=$moderatorId&passcode=$moderatorPasscode");
    }
}
