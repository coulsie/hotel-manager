<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;

// ==========================================================================
// ROUTES D'AUTHENTIFICATION (Accessibles publiquement sans connexion)
// ==========================================================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================================================
// SECTEUR SÉCURISÉ : TOUTES LES ROUTES CI-DESSOUS DEMANDENT UNE CONNEXION
// ==========================================================================
Route::middleware(['auth'])->group(function () {

    // 1. Accueil / Tableau de bord
    Route::get('/', [DashboardController::class, 'index']);

    // 2. Arrivées / Check-in
    Route::get('/checkin', [RoomController::class, 'showCheckinForm'])->name('checkin.form');
    Route::post('/checkin', [RoomController::class, 'processCheckin'])->name('checkin.save');

    // 3. Commandes et Restauration
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');

    // 4. État des Chambres et Entretien (Housekeeping)
    Route::get('/rooms/status', [RoomController::class, 'statusGrid'])->name('rooms.status');
    Route::get('/housekeeping', [RoomController::class, 'housekeepingIndex'])->name('housekeeping.index');
    Route::post('/housekeeping/room/{room}/status', [RoomController::class, 'updateHousekeepingStatus'])->name('housekeeping.update');

    // 5. Billetterie côté Comptoir / Front-Desk
    Route::get('/bookings/shows', [ShowController::class, 'showBookingForm'])->name('bookings.shows.form');
    Route::post('/bookings/shows', [ShowController::class, 'processShowBooking'])->name('bookings.shows.save');

    // 6. IoT Logs
    Route::get('/iot/access-logs', [RoomController::class, 'accessLogsIndex'])->name('iot.logs.index');

    // 7. Module Comptabilité, Facturation & Grand Livre
    Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/payments', [BillingController::class, 'paymentsHistory'])->name('payments');
        Route::get('/room/{room}', [BillingController::class, 'showRoomInvoice'])->name('room');
        Route::post('/restaurant/{order}', [BillingController::class, 'storeRestaurantPayment'])->name('restaurant.pay');
        Route::post('/room/{room}', [BillingController::class, 'storeRoomPayment'])->name('room.pay');
        Route::get('/{id}/print', [BillingController::class, 'printReceipt'])->name('print');
        Route::delete('/payments/{id}', [BillingController::class, 'destroyPayment'])->name('payments.destroy');

    });

    // 8. Module d'Administration centralisé (Salles, Concerts, Personnel)
    Route::prefix('admin')->name('admin.')->group(function () {
        // Gestion des Chambres
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

        // Gestion de la Carte / Menu Restauration
        Route::get('/menu/create', [OrderController::class, 'createMenuItem'])->name('menu.create');
        Route::post('/menu', [OrderController::class, 'storeMenuItem'])->name('menu.store');

        // État et Infrastructures des SALLES (shows.index.blade.php avec modales)
        Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');
        Route::post('/shows', [ShowController::class, 'store'])->name('shows.store');
        Route::put('/shows/{id}', [ShowController::class, 'update'])->name('shows.update');

        // Programmation des Spectacles Multi-Tarifs (Module Events)
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');

        // 👇 INTÉGRATION SÉCURISÉE : Rapport d'analyse et état des ventes d'un concert donné
        Route::get('/events/{id}/sales', [EventController::class, 'salesDashboard'])->name('events.sales');

        // Gestion des Utilisateurs & Habilitations du personnel
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    });
});
