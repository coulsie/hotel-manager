<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hotel Manager')</title>
    <!-- Chargement de Bootstrap & Chart.js via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-size: .875rem;
            padding-top: 60px;
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Navbar Haute Moderne avec Dégradé Indigo / Bleu Nuit */
        .navbar-custom {
            height: 60px;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #1e3a8a 100%);
            box-shadow: 0 4px 15px rgba(30, 27, 75, 0.25);
        }
        .navbar-brand {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            background: rgba(255, 255, 255, 0.08);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s;
        }
        .navbar-brand:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        /* Menu Latéral Propre & Coloré */
        .sidebar {
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 16px 0 24px 0;
            background-color: #ffffff !important;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.05);
            overflow-y: auto;
        }

        /* Barre de défilement fine pour le menu */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        .sidebar .nav-link {
            font-weight: 600;
            color: #475569;
            padding: 0.65rem 1.1rem;
            margin: 3px 10px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }
        .sidebar .nav-link i {
            font-size: 1.15rem;
            margin-right: 10px;
            transition: transform 0.2s, color 0.2s;
        }
        .sidebar .nav-link:hover {
            color: #2563eb;
            background-color: #eff6ff;
            transform: translateX(3px);
        }
        .sidebar .nav-link:hover i {
            transform: scale(1.12);
        }

        /* État Actif Éclatant (Dégradé Bleu Royal / Indigo) */
        .sidebar .nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
            font-weight: 700;
        }
        .sidebar .nav-link.active i {
            color: #fde047 !important; /* Jaune doré éclatant sur le fond bleu */
        }

        /* Titres de sections thématiques (Headings) */
        .sidebar-heading {
            font-size: 0.72rem;
            letter-spacing: 0.8px;
            font-weight: 800;
            text-transform: uppercase;
            padding: 0.5rem 1rem;
            margin: 1.2rem 10px 0.4rem 10px;
            border-radius: 8px;
            border-left: 4px solid currentColor;
        }

        /* Arrière-plans et couleurs thématiques par pôle */
        .bg-light-indigo  { background-color: #eef2ff; color: #4338ca !important; }
        .bg-light-amber   { background-color: #fef3c7; color: #b45309 !important; }
        .bg-light-emerald { background-color: #d1fae5; color: #047857 !important; }
        .bg-light-pink    { background-color: #fce7f3; color: #be185d !important; }
        .bg-light-slate   { background-color: #f1f5f9; color: #334155 !important; }

        /* Bouton de déconnexion */
        .bg-light-danger { background-color: #fee2e2; color: #b91c1c !important; transition: all 0.2s; }
        .bg-light-danger:hover { background-color: #fecaca !important; color: #991b1b !important; }

        /* Palette d'icônes individuelles */
        .text-indigo  { color: #4f46e5 !important; }
        .text-teal    { color: #0d9488 !important; }
        .text-purple  { color: #7c3aed !important; }
        .text-orange  { color: #ea580c !important; }
        .text-emerald { color: #10b981 !important; }

        /* Conteneur principal */
        main {
            padding-top: 24px;
            padding-bottom: 40px;
        }
        .main-card-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
            padding: 24px;
            min-height: 82vh;
        }
    </style>
</head>
<body>

    <!-- Barre supérieure -->
    <header class="navbar navbar-dark fixed-top navbar-custom flex-md-nowrap p-0">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 text-white" href="{{ url('/') }}">
            <i class="bi bi-building-fill-check text-warning me-2 fs-4"></i>
            <span>HOTEL <span class="text-info">MANAGER</span></span>
        </a>

        <!-- Bouton menu mobile -->
        <button class="navbar-toggler position-absolute d-md-none collapsed border-0 me-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Menu" style="right: 120px;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Affichage dynamique de l'utilisateur et de son rôle -->
        <div class="navbar-nav ms-auto pe-4">
            <div class="nav-item text-nowrap d-flex align-items-center gap-2">
                @auth
                    @php
                        $userRole = Auth::user()->roles->first()->name ?? 'Employé';
                    @endphp
                    <span class="badge bg-white text-dark px-3 py-2 rounded-pill fw-bold shadow-sm">
                        <i class="bi bi-person-circle text-primary me-1"></i> {{ Auth::user()->name }}
                        <span class="badge bg-primary text-white rounded-pill ms-1 text-uppercase" style="font-size: 0.65rem;">{{ $userRole }}</span>
                    </span>
                @endauth
            </div>
        </div>
    </header>

        <div class="container-fluid">
        <div class="row">
            <!-- Menu latéral -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse border-end">
                <div class="pt-1">
                    <ul class="nav flex-column mb-4 px-1">
                        <!-- Tableau de bord général -->
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::is('/') ? 'active' : '' }} fw-bold" href="{{ url('/') }}">
                                <i class="bi bi-speedometer2 text-primary fs-5"></i>
                                <span>Tableau de bord</span>
                            </a>
                        </li>

                        <!-- SECTION : Chambres & IoT -->
                        <h6 class="sidebar-heading d-flex align-items-center bg-light-indigo">
                            <i class="bi bi-cpu-fill me-2"></i><span>Chambres & IoT</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('checkin.form') ? 'active' : '' }}" href="{{ route('checkin.form') }}">
                                <i class="bi bi-person-plus-fill text-primary"></i>
                                <span>Réservation / Check-in</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('iot.logs.index') ? 'active' : '' }}" href="{{ route('iot.logs.index') }}">
                                <i class="bi bi-broadcast-pin text-indigo"></i>
                                <span>Suivi des Cartes RFID</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('rooms.status') ? 'active' : '' }}" href="{{ route('rooms.status') }}">
                                <i class="bi bi-door-closed-fill text-teal"></i>
                                <span>État des Chambres</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::routeIs('housekeeping.index') ? 'active' : '' }}" href="{{ route('housekeeping.index') }}">
                                <i class="bi bi-stars text-purple"></i>
                                <span>Planification Ménage</span>
                            </a>
                        </li>

                        <!-- SECTION : Restauration -->
                        <h6 class="sidebar-heading d-flex align-items-center bg-light-amber">
                            <i class="bi bi-cup-hot-fill me-2"></i><span>Restauration</span>
                        </h6>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::routeIs('orders.create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                                <i class="bi bi-egg-fried text-warning"></i>
                                <span>Prise de Commande</span>
                            </a>
                        </li>

                        <!-- SECTION : Comptabilité -->
                        <h6 class="sidebar-heading d-flex align-items-center bg-light-emerald">
                            <i class="bi bi-cash-stack me-2"></i><span>Comptabilité</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('billing.index') ? 'active' : '' }}" href="{{ route('billing.index') }}">
                                <i class="bi bi-calculator-fill text-success"></i>
                                <span>Encours & Factures</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::routeIs('billing.payments') ? 'active' : '' }}" href="{{ route('billing.payments') }}">
                                <i class="bi bi-cash-coin text-emerald"></i>
                                <span>Grand Livre des recettes</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::routeIs('billing.daily.close') ? 'active' : '' }}" href="{{ route('billing.daily.close') }}">
                                <i class="bi bi-safe2-fill text-teal"></i>
                                <span>Clôture Caisse (Rapport Z)</span>
                            </a>
                        </li>

                        <!-- SECTION : Culture & Événements -->
                        <h6 class="sidebar-heading d-flex align-items-center bg-light-pink">
                            <i class="bi bi-music-note-beamed me-2"></i><span>Culture & Billetterie</span>
                        </h6>
                        <li class="nav-item mb-2">
                            <a class="nav-link {{ Request::routeIs('bookings.shows.form') ? 'active' : '' }}" href="{{ route('bookings.shows.form') }}">
                                <i class="bi bi-ticket-perforated-fill text-danger"></i>
                                <span>Émettre un Billet</span>
                            </a>
                        </li>

                        <!-- SECTION : Configurations Hôtelières (Administrateur uniquement) -->
                        @can('configurer_salles')
                        <h6 class="sidebar-heading d-flex align-items-center bg-light-slate">
                            <i class="bi bi-gear-fill me-2"></i><span>Configuration</span>
                        </h6>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.shows.index') ? 'active' : '' }}" href="{{ route('admin.shows.index') }}">
                                <i class="bi bi-building-fill text-primary"></i>
                                <span>Salles de Spectacle</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.events.index') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                                <i class="bi bi-calendar-event-fill text-orange"></i>
                                <span>Grilles & Concerts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ Request::routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="bi bi-shield-lock-fill text-indigo"></i>
                                <span>Gestion des Utilisateurs</span>
                            </a>
                        </li>
                        @endcan

                        <!-- Déconnexion sécurisée -->
                        <li class="nav-item mt-4 border-top pt-3 px-2">
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                                @csrf
                            </form>
                            <a class="nav-link fw-bold rounded-3 bg-light-danger justify-content-center" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-left fs-5"></i>
                                <span>Déconnexion</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- ZONE PRINCIPALE : Injection dynamique des vues de l'application -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="main-card-container">
                    @yield('content')
                </div>

                <footer class="text-center text-muted small mt-4 pb-2 d-print-none">
                    <strong>Hotel Manager Resort</strong> • Système de Gestion Hôtelière, Restauration, Billetterie & Sécurité IoT
                </footer>
            </main>
        </div>
    </div>

</body>
</html>
