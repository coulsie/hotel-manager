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
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\GuestPortalController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\ConferenceSpaceController;
use App\Http\Controllers\PublicBookingController;

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

    // 2. Réception : Arrivées, Chambres & IoT
    Route::controller(RoomController::class)->group(function () {
        // Check-in
        Route::middleware('permission:gerer_checkin')->prefix('checkin')->name('checkin.')->group(function () {
            Route::get('/', 'showCheckinForm')->name('form');
            Route::post('/', 'processCheckin')->name('save');
        });

        // Gestion des séjours & État des chambres
        Route::middleware('permission:voir_chambres')->prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/status', 'statusGrid')->name('status');
            Route::post('/{room}/checkout', 'earlyCheckout')->name('checkout');
            Route::post('/{room}/extend', 'extendStay')->name('extend');
        });

        // Journaux d'accès IoT
        Route::middleware('permission:voir_iot')->get('/iot/access-logs', 'accessLogsIndex')->name('iot.logs.index');
    });

    // 3. Restauration & Commandes
    Route::middleware('permission:gerer_restauration')->prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{order}/receipt', 'showReceipt')->name('receipt');
    });

    // 4. Services Annexes : Pressing & Lavage Auto
    Route::middleware('permission:gerer_services')->prefix('services')->name('services.')->controller(ExtraServiceController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');

        // Gestion du catalogue tarifaire (avant les routes paramétrées)
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::post('/', 'storeCatalog')->name('store');
            Route::put('/{catalog}', 'updateCatalog')->name('update');
            Route::delete('/{catalog}', 'destroyCatalog')->name('destroy');
        });

        // Suivi atelier & encaissement
        Route::post('/{service}/ready', 'markReady')->name('ready');
        Route::post('/{service}/pay', 'payExternal')->name('pay');
    });

    // 5. Billetterie Comptoir (Front-Desk)
    Route::middleware('permission:gerer_billetterie')->prefix('bookings/shows')->name('bookings.shows.')->controller(ShowController::class)->group(function () {
        Route::get('/', 'showBookingForm')->name('form');
        Route::post('/', 'processShowBooking')->name('save');
    });

    // 6. Comptabilité, Facturation & Grand Livre
    Route::prefix('billing')->name('billing.')->controller(BillingController::class)->group(function () {
        // Encours & règlements
        Route::middleware('permission:gerer_encours')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/room/{room}', 'showRoomInvoice')->name('room');
            Route::post('/room/{room}', 'storeRoomPayment')->name('room.pay');
            Route::post('/room/{room}/checkout', 'checkoutRoom')->name('room.checkout');
            Route::post('/restaurant/{order}', 'storeRestaurantPayment')->name('restaurant.pay');
        });

        // Grand Livre & Export
        Route::middleware('permission:voir_grand_livre')->group(function () {
            Route::get('/payments', 'paymentsHistory')->name('payments');
            Route::get('/payments/export', 'exportPaymentsCsv')->name('payments.export');
        });

        // Suppression de reçu réservée à l'administrateur
        Route::middleware('role:admin')->delete('/payments/{id}', 'destroyPayment')->name('payments.destroy');

        // Rapport Z (Clôture de caisse)
        Route::middleware('permission:voir_rapport_z')->get('/daily-close', 'dailyCloseReport')->name('daily.close');

        // Impression de reçu (accessible à tout agent habilité à encaisser)
        Route::get('/{id}/print', 'printReceipt')->name('print');
    });

    // 7. Administration Centralisée (Sécurisée par permission configurer_salles)
    Route::middleware('permission:configurer_salles')->prefix('admin')->name('admin.')->group(function () {

        // Gestion des Chambres
        Route::prefix('rooms')->name('rooms.')->controller(RoomController::class)->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::put('/{room}', 'update')->name('update');
            Route::delete('/{room}', 'destroy')->name('destroy');
        });

        // Gestion de la Carte / Menu Restauration
        Route::prefix('menu')->name('menu.')->controller(OrderController::class)->group(function () {
            Route::get('/create', 'createMenuItem')->name('create');
            Route::post('/', 'storeMenuItem')->name('store');
            Route::put('/{menuItem}', 'updateMenuItem')->name('update');
            Route::delete('/{menuItem}', 'destroyMenuItem')->name('destroy');
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
                // Gestion des Utilisateurs & Habilitations
        Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::post('/{id}/reset-password', 'resetPassword')->name('reset.password'); // <-- À ajouter ici
            Route::post('/roles/{role}/permissions', 'updateRolePermissions')->name('roles.permissions');
        });

        // Sauvegarde de la base de données (Admin uniquement)
        Route::middleware('role:admin')->get('/backup/download', [BackupController::class, 'downloadSql'])->name('backup.download');


    });

    // 8. Réservations Futures, Rack Hôtelier & Fichier Clients
    Route::prefix('reservations')->name('reservations.')->controller(ReservationController::class)->group(function () {
        Route::middleware('permission:voir_rack')->get('/rack', 'rack')->name('rack');

        Route::middleware('permission:gerer_checkin')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{reservation}', 'update')->name('update');
            Route::delete('/{reservation}', 'destroy')->name('destroy');
            Route::post('/{reservation}/checkin', 'convertToCheckin')->name('checkin');
            Route::post('/{reservation}/cancel', 'cancel')->name('cancel');
            Route::get('/{reservation}/police-form', 'policeForm')->name('police');
        });
    });

    // 9. Gouvernance, Planning Hebdomadaire & Audit Ménage
    Route::middleware('permission:gerer_menage')->prefix('housekeeping')->name('housekeeping.')->controller(HousekeepingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/report', 'report')->name('report');
        Route::post('/agents', 'storeAgent')->name('agents.store');
        Route::post('/assign', 'assignRoom')->name('assign');
        Route::post('/assign-week', 'assignWeek')->name('assign.week');
        Route::post('/{room}/clean', 'markAsClean')->name('clean');
        Route::post('/{room}/daily-clean', 'markDailyClean')->name('daily.clean');
        Route::post('/{room}/undo-daily-clean', 'undoDailyClean')->name('undo.daily.clean');
        Route::post('/{room}/incident', 'reportIncident')->name('incident');
        Route::post('/{room}/resolve-maintenance', 'resolveMaintenance')->name('resolve.maintenance');
    });

    // 10. Activités & Services Annexes (Piscine, Spa, Coworking, Séminaire, Navette)
        Route::middleware('permission:gerer_activites')->prefix('activities')->name('activities.')->controller(ActivityController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/catalog', 'storeCatalog')->name('catalog.store');
        Route::put('/catalog/{id}', 'updateCatalog')->name('catalog.update');
        Route::delete('/catalog/{id}', 'destroyCatalog')->name('catalog.destroy');
        Route::post('/book', 'storeBooking')->name('book');
        Route::post('/{id}/pay', 'markAsPaid')->name('pay');
    });


    // 11. Dépenses & Petite Caisse (Sorties de fonds)
    Route::middleware('permission:gerer_depenses')->prefix('expenses')->name('expenses.')->controller(ExpenseController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update'); // <-- À ajouter ici
        Route::delete('/{id}', 'destroy')->name('destroy');
    });



    // 12. Gestion des Stocks & Économat
    Route::middleware('permission:gerer_stock')->prefix('stock')->name('stock.')->controller(StockController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/items', 'storeItem')->name('items.store');
        Route::post('/movements', 'storeMovement')->name('movements.store');
    });


    // PORTAIL CLIENT EN CHAMBRE (Accès via QR Code sans mot de passe staff)
    // Gestion des QR Codes & Demandes Clients en Chambre
    Route::middleware('permission:voir_chambres')->prefix('room-directory')->name('room.directory.')->controller(GuestPortalController::class)->group(function () {
        Route::get('/qrcodes', 'qrCodesIndex')->name('qrcodes');
        Route::post('/requests/{id}/complete', 'completeRequest')->name('complete');
    });



    // 13. Fichier Clients CRM, Fidélité VIP & Comptes Entreprises
    Route::middleware('permission:gerer_crm')->prefix('crm')->name('crm.')->controller(CrmController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/companies', 'storeCompany')->name('companies.store');
        Route::put('/customers/{id}', 'updateCustomer')->name('customers.update');
        Route::get('/companies/{id}/statement', 'companyStatement')->name('companies.statement');
    });



    // 14. Ressources Humaines : Planning des Quarts, Pointage & Avances
    Route::middleware('permission:gerer_rh')->prefix('hr')->name('hr.')->controller(HrController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/shifts', 'storeShift')->name('shifts.store');
        Route::post('/attendance', 'storeAttendance')->name('attendance.store');
        Route::post('/advances', 'storeAdvance')->name('advances.store');
    });

    Route::resource('conferences', ConferenceSpaceController::class);
    Route::post('conferences/bookings', [ConferenceSpaceController::class, 'storeBooking'])->name('conferences.bookings.store');
    Route::patch('conferences/bookings/{booking}/status', [ConferenceSpaceController::class, 'updateBookingStatus'])->name('conferences.bookings.status');

    Route::get('/qr-code', function (\Illuminate\Http\Request $request) {
        $text = (string) $request->query('text', 'https://wave.com');

        $generator = new \SimpleSoftwareIO\QrCode\Generator();
        $svg = $generator->format('svg')->size(160)->margin(1)->generate($text);

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    })->name('qr.generate');



Route::get('/reserver', [PublicBookingController::class, 'index'])->name('public.booking.index');
Route::post('/reserver', [PublicBookingController::class, 'store'])->name('public.booking.store');




});
// ==========================================================================
// 0 BIS. PORTAIL CLIENT EN CHAMBRE (Accès public via QR Code)
// ==========================================================================
Route::prefix('room-service')->name('guest.')->controller(GuestPortalController::class)->group(function () {
    Route::get('/chambre/{room}', 'showRoomPortal')->name('portal');
    Route::post('/chambre/{room}/order', 'storeRoomOrder')->name('order');
    Route::post('/chambre/{room}/request', 'storeRoomRequest')->name('request');
});
