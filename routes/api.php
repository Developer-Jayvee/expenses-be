<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\OptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('login',[AuthController::class,'login'])->name('auth.login');

Route::middleware(["auth.cookie"])->group(function() { 
    
    Route::post('logout',[AuthController::class,'logout'])->name('auth.logout');


    Route::apiResource('bills',BillsController::class);
    
    // Options
    Route::get("options/{type}",OptionController::class);

    // Authentication checker
    Route::get('auth-check', function (){
        return response()->json(['message' => 'Authenticated']);
    });
});