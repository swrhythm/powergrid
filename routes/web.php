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
