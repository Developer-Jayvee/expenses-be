<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\DailyBudgetsController;
use App\Http\Controllers\DailyExpensesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware(['auth.cookie'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Bills
    Route::prefix('bills')->group(function () {
        Route::patch('{id}/update', [BillsController::class, 'update']);
        Route::get('{id}/details', [BillsController::class, 'show']);
        Route::delete('{id}/delete', [BillsController::class, 'destroy']);
        Route::get('{bill}/nextBill', [BillsController::class, 'getNextBill']);
        Route::get('{bill}/activities', [ActivityController::class, 'index']);

    });
    Route::apiResource('bills', BillsController::class)->only(['index', 'store']);

    // Transaction
    Route::prefix('transaction')->group(function () {
        Route::get('{bill}/list', [TransactionController::class, 'index']);
        Route::post('create', [TransactionController::class, 'createTransaction']);
        Route::delete('{id}/delete', [TransactionController::class, 'destroy']);
    });

    // Daily Expenses
    Route::prefix('daily-budgets')->group(function () {
        Route::get('/', [DailyBudgetsController::class, 'index']);
        Route::post('/', [DailyBudgetsController::class, 'store']);
        Route::get('active', [DailyBudgetsController::class, 'active']); // before {id}
        Route::get('{id}/details', [DailyBudgetsController::class, 'show']);
        Route::patch('{id}/done', [DailyBudgetsController::class, 'done']);
        Route::patch('{id}/cancel', [DailyBudgetsController::class, 'cancel']);
        Route::post('{budget}/expenses', [DailyExpensesController::class, 'store']);
        Route::delete('expenses/{id}/delete', [DailyExpensesController::class, 'destroy']);
    });

    // Dashboard
    Route::get('dashboard/summary', [DashboardController::class, 'summary']);

    // Options
    Route::get('options/{type}', OptionController::class);

    // Authentication checker
    Route::get('auth-check', function () {
        return response()->json(['message' => 'Authenticated']);
    });
});
