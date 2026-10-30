<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoorController; // Pensez à ajouter cette ligne

// Vous pouvez laisser ou supprimer la route exemple 'user' par défaut
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// La route pour votre boîtier IoT de gestion des portes
Route::post('/v1/door/verify-access', [DoorController::class, 'verifyAccess']);
