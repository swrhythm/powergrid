<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

Route::get('/addPlayer', [App\Http\Controllers\PlayerController::class, 'addPlayer'])->name('addPlayer');
Route::post('/addPlayer', [App\Http\Controllers\PlayerController::class, 'addPlayerProcess'])->name('addPlayer');
Route::get('/viewPlayer', [App\Http\Controllers\PlayerController::class, 'viewPlayer'])->name('viewPlayer');
Route::get('/playerDetail', [App\Http\Controllers\PlayerController::class, 'playerDetails'])->name('playerDetail');
Route::post('/inputPlayerTransaction', [App\Http\Controllers\PlayerController::class, 'inputPlayerTransaction'])->name('inputPlayerTransaction');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Game Session & Market routes
Route::post('/gameSession/setup',             [App\Http\Controllers\GameSessionController::class, 'setup'])->name('gameSession.setup');
Route::post('/gameSession/advanceStep',       [App\Http\Controllers\GameSessionController::class, 'advanceStep'])->name('gameSession.advanceStep');
Route::post('/gameSession/buyResource',       [App\Http\Controllers\GameSessionController::class, 'buyResource'])->name('gameSession.buyResource');
Route::post('/gameSession/refillMarket',      [App\Http\Controllers\GameSessionController::class, 'refillMarket'])->name('gameSession.refillMarket');
Route::post('/gameSession/addPowerplant',     [App\Http\Controllers\GameSessionController::class, 'addPowerplant'])->name('gameSession.addPowerplant');
Route::post('/gameSession/replacePowerplant', [App\Http\Controllers\GameSessionController::class, 'replacePowerplant'])->name('gameSession.replacePowerplant');

// Public market view (no auth required — for TV/big screen)
Route::get('/market/{moderatorId}',           [App\Http\Controllers\GameSessionController::class, 'marketView'])->name('market.view');

// Guided onboarding flow
Route::get('/createSession',    [App\Http\Controllers\SessionSetupController::class, 'createSessionForm'])->name('createSession');
Route::post('/createSession',   [App\Http\Controllers\SessionSetupController::class, 'createSession']);
Route::get('/sessionCreated',   [App\Http\Controllers\SessionSetupController::class, 'sessionCreated'])->name('sessionCreated');
Route::get('/setupPlayers',     [App\Http\Controllers\SessionSetupController::class, 'setupPlayersForm'])->name('setupPlayers');
Route::post('/setupPlayers',    [App\Http\Controllers\SessionSetupController::class, 'setupPlayers']);
Route::get('/changePassword',   [App\Http\Controllers\PlayerController::class, 'changePasswordForm'])->name('changePassword');
Route::post('/changePassword',  [App\Http\Controllers\PlayerController::class, 'changePassword']);
