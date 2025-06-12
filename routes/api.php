<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationSentController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\RoleController;
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
    Route::get('/tickets-referenceData', [TicketController::class, 'getReferenceData']);
    Route::post('/tickets', [TicketController::class, 'create']);
    Route::put('/assign-ticket/{id}', [TicketController::class, 'assignTicket']);
    Route::put('/tickets/{id}', [TicketController::class, 'update']);
   
    Route::get('attachments/{id}/download', [AttachmentController::class, 'download']);

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    
    Route::post('/send-message/{conversationId}', [MessageController::class, 'store']);
    Route::get('/get-messages/{id}', [MessageController::class, 'getMessages']);
    Route::get('/get-conversations', [MessageController::class, 'getConversations']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/user-roles/{id}', [RoleController::class, 'getUsersForRole']);
    Route::get('/get-role/{id}', [RoleController::class, 'show']);
    Route::get('get-permissions', [RoleController::class, 'getAllPermissions']);
    Route::post('/roles', [RoleController::class, 'create']);
    Route::delete('/roles/{id}', [RoleController::class, 'delete']);
    Route::put('/roles/{id}', [RoleController::class, 'update']);
    Route::post('/roles/{roleId}/assign', [RoleController::class, 'assignRole']);
    Route::post('/roles/{roleId}/revoke', [RoleController::class, 'revokeRole']);

    Route::get('/dashboard', [DashboardController::class, 'index']);

});