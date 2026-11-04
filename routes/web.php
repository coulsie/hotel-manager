<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\EventController;

use App\Http\Controllers\BillingController; // <-- CETTE LIGNE DOIT EXISTER


// Accueil / Tableau de bord
Route::get('/', [DashboardController::class, 'index']);

// Arrivées / Check-in
Route::get('/checkin', [RoomController::class, 'showCheckinForm'])->name('checkin.form');
Route::post('/checkin', [RoomController::class, 'processCheckin'])->name('checkin.save');

// Commandes et Restauration
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');

// État des Chambres et Entretien (Housekeeping)
Route::get('/rooms/status', [RoomController::class, 'statusGrid'])->name('rooms.status');
Route::get('/housekeeping', [RoomController::class, 'housekeepingIndex'])->name('housekeeping.index');
Route::post('/housekeeping/room/{room}/status', [RoomController::class, 'updateHousekeepingStatus'])->name('housekeeping.update');

// Billetterie côté Comptoir / Front-Desk
Route::get('/bookings/shows', [ShowController::class, 'showBookingForm'])->name('bookings.shows.form');
Route::post('/bookings/shows', [ShowController::class, 'processShowBooking'])->name('bookings.shows.save');

// IoT Logs
Route::get('/iot/access-logs', [RoomController::class, 'accessLogsIndex'])->name('iot.logs.index');

// ==========================================================================
// MODULE COMPTABILITÉ, FACTURATION & GRAND LIVRE (Unifié sous BillingController)
// ==========================================================================
// MODULE COMPTABILITÉ, FACTURATION & GRAND LIVRE

// LE GROUPE UNIQUE ET OFFICIEL DE FACTURATION
Route::group(['prefix' => 'billing', 'as' => 'billing.'], function () {

    // Page 1 : Les Encours (Appelle billing/index.blade.php)
    Route::get('/', [BillingController::class, 'index'])->name('index');

    // Page 2 : L'Historique (Appelle billing/payments.blade.php)
    Route::get('/payments', [BillingController::class, 'paymentsHistory'])->name('payments');

    // Page 3 : Détails d'une facture de chambre (Appelle billing/room_invoice.blade.php)
    Route::get('/room/{room}', [BillingController::class, 'showRoomInvoice'])->name('room');

    // Traitements des encaissements et impression
    Route::post('/restaurant/{order}', [BillingController::class, 'storeRestaurantPayment'])->name('restaurant.pay');
    Route::post('/room/{room}', [BillingController::class, 'storeRoomPayment'])->name('room.pay');
    Route::get('/{id}/print', [BillingController::class, 'printReceipt'])->name('print');
});

// ==========================================================================
// MODULE D'ADMINISTRATION (Gestion des Infrastructures & Programmation)
// ==========================================================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Gestion des Chambres
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');

    // Gestion de la Carte / Menu
    Route::get('/menu/create', [OrderController::class, 'createMenuItem'])->name('menu.create');
    Route::post('/menu', [OrderController::class, 'storeMenuItem'])->name('menu.store');

    // Gestion des Salles (Infrastructures / Venues)
    Route::get('/venues', [ShowController::class, 'venueIndex'])->name('venues.index');
    Route::get('/venues/create', [ShowController::class, 'venueCreate'])->name('venues.create');
    Route::post('/venues', [ShowController::class, 'venueStore'])->name('venues.store');

    // Anciennes routes Shows
    Route::get('/shows/create', [ShowController::class, 'create'])->name('shows.create');
    Route::post('/shows', [ShowController::class, 'store'])->name('shows.store');

    // Programmation des Spectacles Multi-Tarifs (Module Events)
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
});

// Administration protégée par authentification
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // État des SALLES
    Route::get('/shows', [ShowController::class, 'index'])->name('shows.index');
    // Traitement de l'enregistrement (Modale - Création)
    Route::post('/shows', [ShowController::class, 'store'])->name('shows.store');
    // Traitement de la mise à jour (Modale - Modification)
    Route::put('/shows/{id}', [ShowController::class, 'update'])->name('shows.update');
});
