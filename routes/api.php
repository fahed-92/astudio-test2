<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderApprovalController;
use App\Http\Controllers\AuthController;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Order routes
    Route::apiResource('orders', OrderController::class);
    Route::post('orders/{order}/submit-approval', [OrderController::class, 'submitForApproval']);
    Route::get('orders/{order}/history', [OrderController::class, 'history']);

    // Approval routes
    Route::post('orders/{order}/approve', [OrderApprovalController::class, 'processApproval']);
    Route::get('pending-approvals', [OrderApprovalController::class, 'pendingApprovals']);
});
