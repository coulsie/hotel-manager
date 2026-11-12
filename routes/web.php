<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ExtraServiceController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Admin\UserController;

// ==========================================================================
// 0. AUTHENTIFICATION (Accès public)
// ==========================================================================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login')->name('login.process');
    Route::post('/logout', 'logout')->name('logout');
});

// ==========================================================================
// SECTEUR SÉCURISÉ (Authentification requise)
// ==========================================================================
Route::middleware(['auth'])->group(function () {

    // 1. Accueil / Tableau de bord
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Réception : Arrivées, Chambres & Entretien (Housekeeping)
    Route::controller(RoomController::class)->group(function () {
        // Check-in
        Route::get('/checkin', 'showCheckinForm')->name('checkin.form');
        Route::post('/checkin', 'processCheckin')->name('checkin.save');

        // Gestion des séjours & État des chambres
        Route::get('/rooms/status', 'statusGrid')->name('rooms.status');
        Route::post('/rooms/{room}/checkout', 'earlyCheckout')->name('rooms.checkout');
        Route::post('/rooms/{room}/extend', 'extendStay')->name('rooms.extend');

        // Entretien (Housekeeping)
        Route::get('/housekeeping', 'housekeepingIndex')->name('housekeeping.index');
        Route::post('/housekeeping/room/{room}/status', 'updateHousekeepingStatus')->name('housekeeping.update');

        // Journaux d'accès IoT
        Route::get('/iot/access-logs', 'accessLogsIndex')->name('iot.logs.index');
    });

    // 3. Restauration & Commandes
    Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{order}/receipt', 'showReceipt')->name('receipt');
    });

    // 4. Services Annexes : Pressing & Lavage Auto
    Route::prefix('services')->name('services.')->controller(ExtraServiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');

        // Suivi atelier & encaissement
        Route::post('/{service}/ready', 'markReady')->name('ready');
        Route::post('/{service}/pay', 'payExternal')->name('pay');

        // Gestion du catalogue tarifaire
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::post('/', 'storeCatalog')->name('store');
            Route::put('/{catalog}', 'updateCatalog')->name('update');
            Route::delete('/{catalog}', 'destroyCatalog')->name('destroy');
        });
    });

    // 5. Billetterie Comptoir (Front-Desk)
    Route::prefix('bookings/shows')->name('bookings.shows.')->controller(ShowController::class)->group(function () {
        Route::get('/', 'showBookingForm')->name('form');
        Route::post('/', 'processShowBooking')->name('save');
    });

    // 6. Comptabilité, Facturation & Grand Livre
    Route::prefix('billing')->name('billing.')->controller(BillingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/daily-close', 'dailyCloseReport')->name('daily.close');
        Route::get('/{id}/print', 'printReceipt')->name('print');

        // Historique et export des paiements
        Route::get('/payments', 'paymentsHistory')->name('payments');
        Route::get('/payments/export', 'exportPaymentsCsv')->name('payments.export');
        Route::delete('/payments/{id}', 'destroyPayment')->name('payments.destroy');

        // Facturation Chambres & Restauration
        Route::get('/room/{room}', 'showRoomInvoice')->name('room');
        Route::post('/room/{room}', 'storeRoomPayment')->name('room.pay');
        Route::post('/restaurant/{order}', 'storeRestaurantPayment')->name('restaurant.pay');
    });

    // 7. Administration Centralisée
    Route::prefix('admin')->name('admin.')->group(function () {

        // Gestion des Chambres
        Route::prefix('rooms')->name('rooms.')->controller(RoomController::class)->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
        });

        // Gestion de la Carte / Menu Restauration
        Route::prefix('menu')->name('menu.')->controller(OrderController::class)->group(function () {
            Route::get('/create', 'createMenuItem')->name('create');
            Route::post('/', 'storeMenuItem')->name('store');
        });

        // Infrastructures des Salles
        Route::prefix('shows')->name('shows.')->controller(ShowController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
        });

        // Programmation des Spectacles & Rapports des ventes
        Route::prefix('events')->name('events.')->controller(EventController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::get('/{id}/sales', 'salesDashboard')->name('sales');
        });

        // Gestion des Utilisateurs & Habilitations
        Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
        });
    });

});
