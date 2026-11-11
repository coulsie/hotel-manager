<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hotel Manager')</title>
    <!-- Chargement de Bootstrap via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Icônes Bootstrap -->
   

    <style>
    body {
        font-size: .875rem;
        padding-top: 56px;
        background-color: #f8fafc;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    /* Navbar Haute Moderne */
    .navbar-custom {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        background-color: rgba(255, 255, 255, 0.05);
        transition: all 0.2s;
    }
    .navbar-brand:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Menu Latéral Propre & Coloré */
    .sidebar {
        position: fixed;
        top: 56px;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 20px 0 0;
        background-color: #ffffff !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.03);
    }
    .sidebar .nav-link {
        font-weight: 500;
        color: #475569;
        padding: 0.65rem 1.25rem;
        margin: 2px 8px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        transition: all 0.2s ease-in-out;
    }
    .sidebar .nav-link i {
        font-size: 1.1rem;
        transition: transform 0.2s, color 0.2s;
    }
    .sidebar .nav-link:hover {
        color: #0284c7;
        background-color: #f0f9ff;
    }
    .sidebar .nav-link:hover i {
        transform: scale(1.1);
    }

    /* État Actif avec Contraste Blanc Garanti */
   /* État Actif Sombre / Foncé (Ardoise Foncée) */
    .sidebar .nav-link.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        font-weight: 600;
    }
    .sidebar .nav-link.active i {
        color: #38bdf8 !important; /* Bleu ciel lumineux pour faire ressortir l'icône sur le fond foncé */
    }

    /* Titres de sections unifiés (Headings) */
    .sidebar-heading {
        font-size: 0.74rem;
        letter-spacing: 0.8px;
        font-weight: 700;
        padding: 0.45rem 1rem;
        margin: 1.25rem 8px 0.4rem 8px;
        border-left: 3px solid currentColor;
    }

    /* Arrière-plans et couleurs thématiques par bloc */
    .bg-light-indigo { background-color: #f5f3ff; color: #4f46e5 !important; }
    .bg-light-amber { background-color: #fffbeb; color: #d97706 !important; }
    .bg-light-emerald { background-color: #ecfdf5; color: #059669 !important; }
    .bg-light-pink { background-color: #fdf2f8; color: #db2777 !important; }
    .bg-light-slate { background-color: #f1f5f9; color: #475569 !important; }

    /* Bouton de déconnexion */
    .bg-light-danger { background-color: #fef2f2; transition: all 0.2s; }
    .bg-light-danger:hover { background-color: #fee2e2 !important; color: #dc2626 !important; }

    /* Palette d'icônes individuelles */
    .text-indigo { color: #6366f1 !important; }
    .text-teal { color: #0d9488 !important; }
    .text-purple { color: #9333ea !important; }
    .text-orange { color: #ea580c !important; }
    .text-emerald { color: #10b981 !important; }

    /* Conteneur principal */
    main {
        padding-top: 24px;
    }
    .main-card-container {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 24px;
        min-height: 80vh;
    }
</style>

</head>
<body>

    <!-- Barre supérieure -->
    <header class="navbar navbar-dark sticky-top navbar-custom flex-md-nowrap p-0">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-center text-white" href="{{ url('/') }}">
            <i class="bi bi-building-fill text-info me-2"></i>Hotel Manager
        </a>
        <div class="navbar-nav w-100 text-end pe-3">
            <div class="nav-item text-nowrap">
                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold border shadow-sm">
                    <i class="bi bi-person-badge-fill text-primary me-1"></i> Session: Admin
                </span>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Menu latéral -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 bg-light sidebar border-end">
                <div class="position-sticky pt-2" style="max-height: calc(100vh - 80px); overflow-y: auto;">

                   <ul class="nav flex-column mb-4 px-2">
                        <!-- Tableau de bord général -->
                        <li class="nav-item mb-3">
                            <a class="nav-link {{ Request::is('/') ? 'active' : '' }} fw-bold border-bottom pb-2 rounded-0 text-dark" href="{{ url('/') }}">
                                <i class="bi bi-speedometer2 text-info me-3 fs-5"></i> Tableau de bord
                            </a>
                        </li>

                        <!-- SECTION : Chambres & IoT -->
                        <h6 class="sidebar-heading d-flex align-items-center px-3 py-1.5 rounded-3 text-uppercase heading-iot bg-light-indigo fw-bold mb-2">
                            <i class="bi bi-cpu me-2"></i><span>Chambres & IoT</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('checkin.form') ? 'active' : '' }}" href="{{ route('checkin.form') }}">
                                <i class="bi bi-person-plus text-primary me-2"></i> Réservation / Check-in
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('iot.logs.index') ? 'active' : '' }}" href="{{ route('iot.logs.index') }}">
                                <i class="bi bi-rfid text-indigo me-2"></i> Suivi des Cartes RFID
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('rooms.status') ? 'active' : '' }}" href="{{ route('rooms.status') }}">
                                <i class="bi bi-door-closed text-teal me-2"></i> État des Chambres
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link {{ Request::routeIs('housekeeping.index') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                                <i class="bi bi-brush text-purple me-2"></i> Planification Ménage
                            </a>
                        </li>

                        <!-- SECTION : Restauration -->
                        <h6 class="sidebar-heading d-flex align-items-center px-3 py-1.5 rounded-3 text-uppercase heading-resto bg-light-amber fw-bold mb-2">
                            <i class="bi bi-cup-hot me-2"></i><span>Restauration</span>
                        </h6>
                        <li class="nav-item mb-3">
                            <a class="nav-link {{ Request::routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                                <i class="bi bi-egg-fried text-warning me-2"></i> Prise de Commande
                            </a>
                        </li>

                        <!-- SECTION : Comptabilité -->
                        <h6 class="sidebar-heading d-flex align-items-center px-3 py-1.5 rounded-3 text-uppercase heading-compta bg-light-emerald fw-bold mb-2">
                            <i class="bi bi-cash-stack me-2"></i><span>Comptabilité</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('billing.index') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                                <i class="bi bi-calculator text-success me-2"></i> Encours & Factures
                            </a>
                        </li>
                        <li class="nav-item mb-3">
                            <a class="nav-link {{ Request::routeIs('billing.payments') ? 'active' : '' }}" href="{{ route('billing.payments') }}">
                                <i class="bi bi-cash-coin text-emerald me-2"></i> Historique Paiements
                            </a>
                        </li>

                        <!-- SECTION : Culture & Événements -->
                        <h6 class="sidebar-heading d-flex align-items-center px-3 py-1.5 rounded-3 text-uppercase heading-culture bg-light-pink fw-bold mb-2">
                            <i class="bi bi-music-note-beamed me-2"></i><span>Culture & Billetterie</span>
                        </h6>
                        <li class="nav-item mb-3">
                            <a class="nav-link {{ Request::routeIs('bookings.shows.form') ? 'active' : '' }}" href="{{ route('bookings.shows.form') }}">
                                <i class="bi bi-ticket-perforated text-danger me-2"></i> Émettre un Billet
                            </a>
                        </li>

                        <!-- SECTION : Configurations Hôtelières (Administrateur uniquement) -->
                        @can('configurer_salles')
                        <h6 class="sidebar-heading d-flex align-items-center px-3 py-1.5 rounded-3 text-uppercase heading-config bg-light-slate fw-bold mb-2">
                            <i class="bi bi-gear-fill me-2"></i><span>Configuration</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.shows.index') ? 'active' : '' }}" href="{{ route('admin.shows.index') }}">
                                <i class="bi bi-building-gear text-secondary me-2"></i> Salles de Spectacle
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.events.index') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                                <i class="bi bi-calendar-event text-orange me-2"></i> Grilles & Concerts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-shield-lock-fill text-dark me-2"></i> Gestion des Utilisateurs
                            </a>
                        </li>
                        @endcan

                        <!-- Déconnexion sécurisée -->
                        <li class="nav-item mt-4 border-top pt-3 mx-2">
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                                @csrf
                            </form>
                            <a class="nav-link text-danger fw-bold rounded-3 bg-light-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left me-2 text-danger fs-5"></i> Déconnexion
                            </a>
                        </li>
                    </ul>

                </div>
            </nav>

            <!-- ZONE PRINCIPALE : Injection dynamique des vues de l'application -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="main-card-container my-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

</body>
</html>
