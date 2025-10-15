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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

// 台灣城市和地區 API
Route::prefix('taiwan')->group(function () {
  Route::get('/cities', [\App\Http\Controllers\Api\TaiwanCitiesController::class, 'getCities']);
  Route::get('/districts/{city}', [\App\Http\Controllers\Api\TaiwanCitiesController::class, 'getDistricts']);
  Route::get('/zipcode/{city}/{district}', [\App\Http\Controllers\Api\TaiwanCitiesController::class, 'getZipcode']);
});
