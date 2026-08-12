<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('login',[AuthController::class,'login'])->name('auth.login');

Route::middleware(["auth.cookie"])->group(function() { 
    Route::post('logout',[AuthController::class,'logout'])->name('auth.logout');

    // Bills
    Route::prefix('bills')->group(function () {
        Route::patch('{id}/update',[BillsController::class, 'update']);
        Route::get('{id}/details',[BillsController::class, 'show']);
        Route::delete('{id}/delete',[BillsController::class, 'destroy']);
    });
    Route::apiResource('bills',BillsController::class)->only(['index','store']);

    // Transaction
    Route::prefix('transaction')->group(function () {
        Route::post('create',[TransactionController::class,'createTransaction']);
    });

    // Options
    Route::get("options/{type}",OptionController::class);

    // Authentication checker
    Route::get('auth-check', function (){
        return response()->json(['message' => 'Authenticated']);
    });
});