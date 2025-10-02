<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Line Webhook
 */
Route::post('/line/webhook', [\App\Http\Controllers\Line\WebhookController::class, 'handle']);

/**
 * Line LIFF Bind
 */
Route::get('/line/liff/bind',  [\App\Http\Controllers\Line\LiffBindController::class, 'form']);
Route::post('/line/liff/bind', [\App\Http\Controllers\Line\LiffBindController::class, 'submit']);
