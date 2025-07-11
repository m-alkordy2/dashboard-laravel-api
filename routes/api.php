<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ItemController;
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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('/login' , [AuthController::class , 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/logout' , [AuthController::class , 'logout']);
    Route::get('/items' , [ItemController::class , 'index']);
    Route::post('/items' , [ItemController::class , 'store']);
    Route::put('/items/{id}' , [ItemController::class , 'update']);
    Route::get('/items/{id}' , [ItemController::class , 'show']);
    Route::delete('/items/{id}' , [ItemController::class , 'destroy']);
});
