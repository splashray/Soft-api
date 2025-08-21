<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Resources\Json\JsonResource;
// use Illuminate\Support\Facades\Route as RouteFacade;
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);


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
    JsonResource::withoutWrapping();
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/balance', [\App\Http\Controllers\Api\TransactionController::class, 'balance']);
    Route::post('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [\App\Http\Controllers\Api\TransactionController::class, 'show']);
});
