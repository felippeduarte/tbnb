<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SimulatedPriceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('stocks', StockController::class)->only(['index','show','store','destroy']);
Route::resource('quotes', QuoteController::class)->only(['index','show','store']);
Route::resource('simulatedPrice', SimulatedPriceController::class)->only(['index']);