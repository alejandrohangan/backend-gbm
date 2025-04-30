<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Rutas de recursos REST estándar
    Route::apiResource('/priorities', PriorityController::class);
    Route::apiResource('/tags', TagController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/tickets', TicketController::class);

    // Rutas personalizadas para tickets
    Route::put('/tickets/{id}/status', [TicketController::class, 'updateStatus']);
    Route::put('/tickets/{id}/close', [TicketController::class, 'closeTicket']);
    Route::get('userTickets', [TicketController::class, 'getUserTickets']);
    Route::get('openTickets', [TicketController::class, 'getOpenTickets']);


    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});