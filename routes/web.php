

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\EventController;


// Remplacer l'ancienne route anonyme par celle reliée au contrôleur
Route::get('/', [DashboardController::class, 'index']);


Route::get('/checkin', [RoomController::class, 'showCheckinForm'])->name('checkin.form');
Route::post('/checkin', [RoomController::class, 'processCheckin'])->name('checkin.save');


Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
Route::get('/rooms/status', [RoomController::class, 'statusGrid'])->name('rooms.status');

// Routes pour la comptabilité et les paiements
Route::get('/billing', [InvoiceController::class, 'index'])->name('billing.index');
Route::get('/billing/room/{room}', [InvoiceController::class, 'showRoomInvoice'])->name('billing.room');
Route::get('/billing/payments', [InvoiceController::class, 'paymentsHistory'])->name('billing.payments');


// Routes de création des Chambres
Route::get('/admin/rooms/create', [RoomController::class, 'create'])->name('admin.rooms.create');
Route::post('/admin/rooms', [RoomController::class, 'store'])->name('admin.rooms.store');

// Routes de création des Repas/Boissons
Route::get('/admin/menu/create', [OrderController::class, 'createMenuItem'])->name('admin.menu.create');
Route::post('/admin/menu', [OrderController::class, 'storeMenuItem'])->name('admin.menu.store');

// Routes de création des Spectacles
Route::get('/admin/shows/create', [ShowController::class, 'create'])->name('admin.shows.create');
Route::post('/admin/shows', [ShowController::class, 'store'])->name('admin.shows.store');
// Routes pour la Réservation de Spectacles
Route::get('/bookings/shows', [ShowController::class, 'showBookingForm'])->name('bookings.shows.form');
Route::post('/bookings/shows', [ShowController::class, 'processShowBooking'])->name('bookings.shows.save');

// Le Check-in des chambres est déjà géré par RoomController (showCheckinForm / processCheckin)
// Nous allons simplement lier la route existante pour le menu


// Routes pour le module d'entretien (Housekeeping)
Route::get('/housekeeping', [RoomController::class, 'housekeepingIndex'])->name('housekeeping.index');
Route::post('/housekeeping/room/{room}/status', [RoomController::class, 'updateHousekeepingStatus'])->name('housekeeping.update');

// Routes pour la gestion des Salles de Spectacle (Infrastructures)
Route::get('/admin/venues', [ShowController::class, 'venueIndex'])->name('admin.venues.index');
Route::post('/admin/venues', [ShowController::class, 'venueStore'])->name('admin.venues.store');


Route::get('/iot/access-logs', [RoomController::class, 'accessLogsIndex'])->name('iot.logs.index');


// Routes pour la gestion des spectacles
Route::get('/admin/events/create', [EventController::class, 'create'])->name('admin.events.create');
Route::post('/admin/events', [EventController::class, 'store'])->name('admin.events.store');



// Route pour afficher la liste des salles (votre page actuelle)
Route::get('/admin/venues', [ShowController::class, 'venueIndex'])->name('admin.venues.index');

// AJOUTER CETTE ROUTE : Formulaire de création d'une salle physique
Route::get('/admin/venues/create', [ShowController::class, 'create'])->name('admin.venues.create');

// AJOUTER CETTE ROUTE : Traitement de l'enregistrement de la salle en BDD
Route::post('/admin/venues', [ShowController::class, 'venueStore'])->name('admin.venues.store');
Route::get('/admin/venues/create', [ShowController::class, 'venueCreate'])->name('admin.venues.create');
