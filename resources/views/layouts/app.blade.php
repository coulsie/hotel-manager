<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hotel Manager')</title>
    <!-- Chargement de Bootstrap via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Icônes Bootstrap (CDN corrigé) -->
    <link rel="stylesheet" href="https://jsdelivr.net">

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
            padding: 24px 0 0;
            background-color: #ffffff !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        }
        .sidebar .nav-link {
            font-weight: 500;
            color: #64748b;
            padding: 0.65rem 1.25rem;
            margin: 2px 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            transition: transform 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #0284c7;
            background-color: #f0f9ff;
        }
        .sidebar .nav-link:hover i {
            transform: scale(1.1);
        }
        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }

        /* Titres de sections (Headings) */
        .sidebar-heading {
            font-size: 0.72rem;
            letter-spacing: 1px;
            font-weight: 700;
            padding: 0.5rem 1.5rem;
            margin-top: 1.5rem !important;
            margin-bottom: 0.25rem;
        }

        /* Couleurs thématiques par bloc */
        .heading-iot { color: #6366f1 !important; }        /* Indigo */
        .heading-resto { color: #f59e0b !important; }      /* Ambre */
        .heading-compta { color: #10b981 !important; }     /* Émeraude */
        .heading-culture { color: #ec4899 !important; }    /* Rose */
        .heading-config { color: #64748b !important; }     /* Ardoise */

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
                    <ul class="nav flex-column mb-4">
                        <li class="nav-item">
                            <a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Tableau de bord
                            </a>
                        </li>

                        <!-- SECTION : Chambres & IoT -->
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 text-uppercase heading-iot">
                            <span>Chambres & IoT</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('checkin.form') ? 'active' : '' }}" href="{{ route('checkin.form') }}">
                                <i class="bi bi-person-plus me-2"></i> Réservation / Check-in
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('iot.logs.index') ? 'active' : '' }}" href="{{ route('iot.logs.index') }}">
                                <i class="bi bi-rfid me-2"></i> Suivi des Cartes RFID
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('rooms.status') ? 'active' : '' }}" href="{{ route('rooms.status') }}">
                                <i class="bi bi-door-closed me-2"></i> État des Chambres
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('housekeeping.index') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                                <i class="bi bi-brush me-2"></i> Planification Ménage
                            </a>
                        </li>

                        <!-- SECTION : Restauration -->
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 text-uppercase heading-resto">
                            <span>Restauration</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                                <i class="bi bi-egg-fried me-2"></i> Prise de Commande
                            </a>
                        </li>

                        <!-- SECTION : Comptabilité -->
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 text-uppercase heading-compta">
                            <span>Comptabilité</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('billing.index') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                                <i class="bi bi-calculator me-2"></i> Encours & Factures
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('billing.payments') ? 'active' : '' }}" href="{{ route('billing.payments') }}">
                                <i class="bi bi-cash-coin me-2"></i> Historique Paiements
                            </a>
                        </li>

                        <!-- SECTION : Culture -->
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 text-uppercase heading-culture">
                            <span>Culture & Événements</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.venues.index') ? 'active' : '' }}" href="{{ route('admin.venues.index') }}">
                                <i class="bi bi-building-gear me-2"></i> Salles de Spectacle
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('bookings.shows.form') ? 'active' : '' }}" href="{{ route('bookings.shows.form') }}">
                                <i class="bi bi-ticket-perforated me-2"></i> Billetterie Spectacle
                            </a>
                        </li>

                        <!-- SECTION : Configuration -->
                        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 text-uppercase heading-config">
                            <span>Configuration</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link text-success fw-bold" href="{{ route('admin.rooms.create') }}">
                                <i class="bi bi-plus-circle-fill me-2 text-success"></i> Nouvelle Chambre
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="{{ route('admin.menu.create') }}">
                                <i class="bi bi-plus-circle-fill me-2 text-warning"></i> Nouveau Repas
                            </a>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.shows.create') }}"><i class="bi bi-theater me-2"></i> + Nouveau Spectacle</a>
                        </li>



                    </ul>
                </div>
            </nav>

            <!-- Contenu de la page -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                @yield('content')
            </main>
        </div>
    </div>

</body>
</html>
