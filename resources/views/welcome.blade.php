@extends('layouts.app')

@section('title', 'Tableau de Bord Statistique - Hotel Manager')

@section('content')
<!-- En-tête de bienvenue et actions rapides -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Tableau de Bord Direction & KPIs
        </h1>
        <p class="text-muted small mb-0 mt-1">
            Activité de l'établissement en temps réel — {{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('reservations.rack') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold shadow-sm">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Rack Hôtelier
        </a>
        <a href="{{ route('checkin.form') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Nouveau Check-in
        </a>
        <a href="{{ route('bookings.shows.form') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-ticket-perforated-fill me-1"></i> Billetterie
        </a>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 1. CARTES INDICATEURS CLÉS (KPI)                                      -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Occupation des Chambres -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Occupation Hôtel</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="bi bi-door-closed-fill fs-5 text-white"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1">{{ $roomsStats }}</h2>
                <div class="small opacity-90">
                    Taux réel : <strong>{{ $occupancyRate }}%</strong> ({{ $availableRooms }} libres)
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Recettes du Jour & Mois -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Recettes du Jour</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="bi bi-cash-coin fs-5 text-white"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1">{{ $revenueStats }}</h3>
                <div class="small opacity-90">
                    Cumul mois : <strong>{{ number_format($monthTotalRevenue ?? 0, 0, ',', ' ') }} F</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 3 : RevPAR & Prix Moyen Chambre (ADR) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">RevPAR / Prix Moyen</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="bi bi-award-fill fs-5 text-white"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1">{{ number_format($revPar ?? 0, 0, ',', ' ') }} F</h3>
                <div class="small opacity-90">
                    Prix moyen loué (ADR) : <strong>{{ number_format($adr ?? 0, 0, ',', ' ') }} F</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 4 : Solde Net Tiroir Espèces (Après Dépenses) -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #ea580c 0%, #d97706 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Caisse Espèces Nette</span>
                    <div class="bg-white bg-opacity-25 rounded-circle p-2">
                        <i class="bi bi-wallet2 fs-5 text-white"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1">{{ number_format($netCashToday ?? 0, 0, ',', ' ') }} F</h3>
                <div class="small opacity-90">
                    Dépenses jour : <strong>- {{ number_format($todayExpenses ?? 0, 0, ',', ' ') }} F</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. GRAPHIQUE FINANCIER (7 JOURS) & ÉTAT DU PARC HÔTELIER              -->
<!-- ===================================================================== -->
<div class="row g-4 mb-4">
    <!-- Courbe d'évolution des recettes (Chart.js) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-graph-up-arrow text-success me-2"></i>Évolution des Recettes sur 7 Jours (F CFA)
                </h6>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 fw-bold">
                    Chambres • Resto • Activités • Pressing • Auto • Spectacles
                </span>
            </div>

            <div class="card-body p-4">
                <div style="height: 290px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Répartition visuelle du parc de chambres -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-pie-chart-fill text-primary me-2"></i>État du Parc ({{ $totalRooms }} Chambres)
                </h6>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                @php
                    $maintCount     = $maintenanceRooms ?? 0;
                    $pctOccupied    = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
                    $pctAvailable   = $totalRooms > 0 ? round(($availableRooms / $totalRooms) * 100) : 0;
                    $pctCleaning    = $totalRooms > 0 ? round(($cleaningRooms / $totalRooms) * 100) : 0;
                    $pctMaintenance = $totalRooms > 0 ? round(($maintCount / $totalRooms) * 100) : 0;
                @endphp

                <div>
                    <!-- Occupées -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span class="text-primary"><i class="bi bi-circle-fill me-1 small"></i> Occupées ({{ $occupiedRooms }})</span>
                            <span>{{ $pctOccupied }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 9px;">
                            <div class="progress-bar bg-primary" style="width: {{ $pctOccupied }}%"></div>
                        </div>
                    </div>

                    <!-- Libres -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span class="text-success"><i class="bi bi-circle-fill me-1 small"></i> Disponibles ({{ $availableRooms }})</span>
                            <span>{{ $pctAvailable }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 9px;">
                            <div class="progress-bar bg-success" style="width: {{ $pctAvailable }}%"></div>
                        </div>
                    </div>

                    <!-- Ménage -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span class="text-warning"><i class="bi bi-circle-fill me-1 small"></i> À Nettoyer ({{ $cleaningRooms }})</span>
                            <span>{{ $pctCleaning }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 9px;">
                            <div class="progress-bar bg-warning" style="width: {{ $pctCleaning }}%"></div>
                        </div>
                    </div>

                                      <!-- Maintenance -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small fw-bold mb-1">
                            <span class="text-danger"><i class="bi bi-circle-fill me-1 small"></i> Maintenance ({{ $maintCount }})</span>
                            <span>{{ $pctMaintenance }}%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 9px;">
                            <div class="progress-bar bg-danger" style="width: {{ $pctMaintenance }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 pt-3 border-top">
                    <a href="{{ route('housekeeping.index') }}" class="btn btn-outline-warning text-dark btn-sm rounded-pill fw-bold">
                        <i class="bi bi-stars me-1"></i> Gouvernance & Ménage
                    </a>
                    <a href="{{ route('rooms.status') }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Plan complet des chambres
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2 BIS. MOUVEMENTS RÉCEPTION DU JOUR (ARRIVÉES & DÉPARTS PRÉVUS)       -->
<!-- ===================================================================== -->

<!-- Barre d'en-tête Mouvements Réception + Bouton Planning Semaine -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h5 class="fw-bold text-dark mb-0">
        <i class="bi bi-arrow-left-right text-primary me-2"></i>Mouvements Réception & Occupation
    </h5>
    <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#weeklyMovementsModal">
        <i class="bi bi-calendar-week-fill me-1"></i> Planning Semaine (Arrivées / Départs)
    </button>
</div>


<div class="row g-4 mb-4">
    <!-- Arrivées attendues aujourd'hui -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header bg-success-subtle text-success border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Arrivées Attendues Aujourd'hui
                </h6>
                <span class="badge bg-success rounded-pill px-3">{{ isset($arrivalsToday) ? $arrivalsToday->count() : 0 }}</span>
            </div>
            <div class="card-body p-3">
                @forelse($arrivalsToday ?? [] as $arr)
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded-3 mb-2 bg-light small">
                        <div>
                            <span class="badge bg-primary me-1">Ch. {{ $arr->room->number ?? '?' }}</span>
                            <strong>{{ $arr->customer->full_name ?? 'Client' }}</strong>
                        </div>
                        <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Check-in</a>
                    </div>
                @empty
                    <p class="text-muted small text-center my-2">Aucune arrivée réservée prévue aujourd'hui.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Départs prévus aujourd'hui -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header bg-danger-subtle text-danger border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bi bi-box-arrow-right me-2"></i>Départs Prévus Aujourd'hui (Check-out)
                </h6>
                <span class="badge bg-danger rounded-pill px-3">{{ isset($departuresToday) ? $departuresToday->count() : 0 }}</span>
            </div>
            <div class="card-body p-3">
                @forelse($departuresToday ?? [] as $dep)
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded-3 mb-2 bg-light small">
                        <div>
                            <span class="badge bg-dark me-1">Ch. {{ $dep->room->number ?? '?' }}</span>
                            <strong>{{ $dep->customer->full_name ?? 'Client' }}</strong>
                        </div>
                        <a href="{{ route('billing.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">Encours / Sortie</a>
                    </div>
                @empty
                    <p class="text-muted small text-center my-2">Aucun départ prévu ce jour.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 3. PROCHAINS SPECTACLES & JOURNAL D'ACCÈS RFID EN DIRECT              -->
<!-- ===================================================================== -->
<div class="row g-4">
    <!-- Tableau des prochains concerts -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="bi bi-calendar-event-fill text-danger me-2"></i>Prochains Concerts Programmés ({{ $showStats }})
                </h6>
                <a href="{{ route('admin.events.index') }}" class="btn btn-sm btn-light border rounded-pill fw-bold">Gérer</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-4">Spectacle</th>
                            <th>Date</th>
                            <th>Places Libres</th>
                            <th class="text-end pe-4">Ventes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($upcomingEvents as $event)
                            @php
                                $cap = $event->tickets->sum('total_capacity');
                                $rem = $event->tickets->sum('available_seats');
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $event->title }}</div>
                                    <small class="text-muted">{{ $event->venue->title ?? 'Salle' }}</small>
                                </td>
                                <td class="small fw-semibold">
                                    {{ \Carbon\Carbon::parse($event->performance_date)->format('d/m H\hi') }}
                                </td>
                                <td>
                                    <span class="badge {{ $rem > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger text-white' }} rounded-pill px-2 py-1 fw-bold">
                                        {{ $rem }} / {{ $cap }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.events.sales', $event->id) }}" class="btn btn-sm btn-outline-success rounded-pill fw-bold">
                                        <i class="bi bi-graph-up"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Aucun concert programmé à venir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tableau des derniers passages RFID (Sécurité IoT) -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold">
                    <i class="bi bi-shield-lock-fill text-info me-2"></i>Derniers Passages RFID ({{ $iotStats }})
                </h6>
                <a href="{{ route('iot.logs.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">Tout voir</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-4">Chambre</th>
                            <th>Badge</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Heure</th>
                        </tr>
                    </thead>
                                        <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">🚪 {{ $log->room->number ?? $log->room_id }}</td>
                                <td class="small font-monospace text-muted">{{ $log->magneticCard->card_uid ?? 'N/A' }}</td>
                                <td>
                                    @if($log->action === 'access_granted')
                                        <span class="badge bg-success rounded-pill px-2"><i class="bi bi-check-lg"></i> Autorisé</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-2"><i class="bi bi-x-lg"></i> Refusé</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 text-muted small">
                                    {{ \Carbon\Carbon::parse($log->scanned_at)->format('H:i:s') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted small">Aucune activité RFID enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODALE : ARRIVÉES & DÉPARTS DE LA SEMAINE (DESIGN PREMIUM & TABLEAUX) -->
<div class="modal fade" id="weeklyMovementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <!-- En-tête dégradé Indigo / Bleu Nuit -->
            <div class="modal-header text-white border-0 py-3 px-4" style="background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 100%);">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-10 p-2 rounded-3">
                        <i class="bi bi-calendar2-week-fill text-warning fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Planning Réception — Arrivées & Départs (7 Jours)</h5>
                        <small class="text-white-50">Suivi prévisionnel des mouvements chambres et rotation gouvernance</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                @php
                    $totalWeekArrivals   = collect($weeklyMovements ?? [])->sum(fn($d) => $d['arrivals']->count());
                    $totalWeekDepartures = collect($weeklyMovements ?? [])->sum(fn($d) => $d['departures']->count());
                @endphp

                <!-- Résumé rapide de la semaine -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-4 border-start border-4 border-primary shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase small fw-bold text-muted">Période couverte</div>
                                <div class="fw-bold text-dark">7 prochains jours</div>
                            </div>
                            <i class="bi bi-calendar-range text-primary fs-3 opacity-50"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-4 border-start border-4 border-success shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase small fw-bold text-muted">Total Arrivées Prévues</div>
                                <div class="fs-5 fw-bold text-success">{{ $totalWeekArrivals }} client(s)</div>
                            </div>
                            <i class="bi bi-box-arrow-in-right text-success fs-3 opacity-50"></i>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-white rounded-4 border-start border-4 border-danger shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-uppercase small fw-bold text-muted">Total Départs Prévus</div>
                                <div class="fs-5 fw-bold text-danger">{{ $totalWeekDepartures }} chambre(s)</div>
                            </div>
                            <i class="bi bi-box-arrow-right text-danger fs-3 opacity-50"></i>
                        </div>
                    </div>
                </div>

                <!-- Onglets de navigation par jour -->
                <div class="bg-white p-2 rounded-4 shadow-sm border mb-4">
                    <ul class="nav nav-pills gap-2 flex-wrap" id="weekDaysTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-bold small px-3" id="tab-all-week" data-bs-toggle="pill" data-bs-target="#pane-all-week" type="button">
                                <i class="bi bi-table me-1"></i> Vue Consolidée Semaine
                            </button>
                        </li>
                        @foreach($weeklyMovements ?? [] as $idx => $dayData)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill fw-bold small px-3" id="tab-day-{{ $idx }}" data-bs-toggle="pill" data-bs-target="#pane-day-{{ $idx }}" type="button">
                                    {{ $dayData['label'] }}
                                    <span class="badge bg-success ms-1" title="Arrivées">{{ $dayData['arrivals']->count() }}</span>
                                    <span class="badge bg-danger ms-1" title="Départs">{{ $dayData['departures']->count() }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="tab-content">
                    <!-- VUE 1 : TABLEAU CONSOLIDÉ DE TOUTE LA SEMAINE -->
                    <div class="tab-pane fade show active" id="pane-all-week">
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 bg-white">
                                    <thead class="text-white small text-uppercase" style="background: linear-gradient(90deg, #1e293b 0%, #334155 100%);">
                                        <tr>
                                            <th class="ps-4 py-3" style="width: 20%;">Jour / Date</th>
                                            <th class="py-3" style="width: 40%;">
                                                <i class="bi bi-box-arrow-in-right text-success me-1"></i> Arrivées Prévues (Check-in)
                                            </th>
                                            <th class="pe-4 py-3" style="width: 40%;">
                                                <i class="bi bi-box-arrow-right text-warning me-1"></i> Départs Prévus (Check-out 12h)
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($weeklyMovements ?? [] as $dayData)
                                            <tr class="{{ $loop->first ? 'table-primary bg-opacity-10' : '' }}">
                                                <td class="ps-4 align-top py-3">
                                                    <div class="fw-bold text-dark">{{ $dayData['label'] }}</div>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                            +{{ $dayData['arrivals']->count() }} Arr.
                                                        </span>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                            -{{ $dayData['departures']->count() }} Dép.
                                                        </span>
                                                    </div>
                                                </td>

                                                <!-- Colonne Arrivées -->
                                                <td class="align-top py-3">
                                                    @forelse($dayData['arrivals'] as $arr)
                                                        <div class="d-flex justify-content-between align-items-center p-2 mb-1 rounded-3 border border-success-subtle bg-success-subtle bg-opacity-10 small">
                                                            <div>
                                                                <span class="badge bg-success me-1">Ch. {{ $arr->room->number ?? '?' }}</span>
                                                                <strong class="text-dark">{{ $arr->customer->full_name ?? 'Client' }}</strong>
                                                            </div>
                                                            <span class="badge bg-white text-dark border">{{ $arr->nights }} nuit(s)</span>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small fst-italic">Aucune arrivée prévue</span>
                                                    @endforelse
                                                </td>

                                                <!-- Colonne Départs -->
                                                <td class="pe-4 align-top py-3">
                                                    @forelse($dayData['departures'] as $dep)
                                                        <div class="d-flex justify-content-between align-items-center p-2 mb-1 rounded-3 border border-danger-subtle bg-danger-subtle bg-opacity-10 small">
                                                            <div>
                                                                <span class="badge bg-danger me-1">Ch. {{ $dep->room->number ?? '?' }}</span>
                                                                <strong class="text-dark">{{ $dep->customer->full_name ?? 'Client' }}</strong>
                                                            </div>
                                                                                                                        <span class="badge {{ $dep->status === 'checked_in' ? 'bg-primary text-white' : 'bg-warning text-dark' }}">
                                                                {{ $dep->status === 'checked_in' ? 'En séjour' : 'Réservé' }}
                                                            </span>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small fst-italic">Aucun départ prévu</span>
                                                    @endforelse
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">Aucune donnée disponible pour cette semaine.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- VUES 2..8 : TABLEAUX DÉTAILLÉS PAR JOUR DE LA SEMAINE -->
                    @foreach($weeklyMovements ?? [] as $idx => $dayData)
                        <div class="tab-pane fade" id="pane-day-{{ $idx }}">
                            <div class="row g-4">
                                <!-- Tableau Arrivées du jour -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                        <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
                                            <span><i class="bi bi-box-arrow-in-right me-2"></i>Arrivées — {{ $dayData['label'] }}</span>
                                            <span class="badge bg-white text-success rounded-pill px-3">{{ $dayData['arrivals']->count() }}</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0 small">
                                                    <thead class="table-light text-uppercase text-muted">
                                                        <tr>
                                                            <th class="ps-4">Chambre</th>
                                                            <th>Client & Téléphone</th>
                                                            <th class="text-end pe-4">Séjour</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($dayData['arrivals'] as $arr)
                                                            <tr>
                                                                <td class="ps-4"><span class="badge bg-success">Ch. {{ $arr->room->number ?? '?' }}</span></td>
                                                                <td>
                                                                    <div class="fw-bold text-dark">{{ $arr->customer->full_name ?? 'Client' }}</div>
                                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $arr->customer->phone ?? '—' }}</div>
                                                                </td>
                                                                <td class="text-end pe-4 fw-bold text-success">{{ $arr->nights }} nuit(s)</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center py-4 text-muted">Aucune arrivée prévue ce jour.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tableau Départs du jour -->
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                                        <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%);">
                                            <span><i class="bi bi-box-arrow-right me-2"></i>Départs — {{ $dayData['label'] }}</span>
                                            <span class="badge bg-white text-danger rounded-pill px-3">{{ $dayData['departures']->count() }}</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle mb-0 small">
                                                    <thead class="table-light text-uppercase text-muted">
                                                        <tr>
                                                            <th class="ps-4">Chambre</th>
                                                            <th>Client & Téléphone</th>
                                                            <th class="text-end pe-4">Statut</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($dayData['departures'] as $dep)
                                                            <tr>
                                                                <td class="ps-4"><span class="badge bg-danger">Ch. {{ $dep->room->number ?? '?' }}</span></td>
                                                                <td>
                                                                    <div class="fw-bold text-dark">{{ $dep->customer->full_name ?? 'Client' }}</div>
                                                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $dep->customer->phone ?? '—' }}</div>
                                                                </td>
                                                                <td class="text-end pe-4">
                                                                    <span class="badge {{ $dep->status === 'checked_in' ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-warning-subtle text-dark border border-warning-subtle' }} rounded-pill px-2">
                                                                        {{ $dep->status === 'checked_in' ? 'En séjour (12h00)' : 'Réservé' }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center py-4 text-muted">Aucun départ prévu ce jour.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer bg-white border-top px-4 py-3">
                <a href="{{ route('reservations.rack') }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold">
                    <i class="bi bi-grid-3x3-gap-fill text-primary me-1"></i> Ouvrir le Rack Hôtelier complet
                </a>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>



<!-- SCRIPT DU GRAPHIQUE FINANCIER (CHART.JS) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const labels         = @json($chartLabels ?? []);
    const lodgingData    = @json($lodgingData ?? []);
    const restaurantData = @json($restaurantData ?? []);
    const activitiesData = @json($activitiesData ?? []);
    const pressingData   = @json($pressingData ?? []);
    const carWashData    = @json($carWashData ?? []);
    const ticketsData    = @json($ticketsData ?? []);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '🏨 Hébergement & Arrhes',
                    data: lodgingData,
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                    barPercentage: 0.75
                },
                {
                    label: '🍽️ Restauration & Bar',
                    data: restaurantData,
                    backgroundColor: '#f59e0b',
                    borderRadius: 6,
                    barPercentage: 0.75
                },
                {
                    label: '🏊 Activités (Piscine/Spa/Navette)',
                    data: activitiesData,
                    backgroundColor: '#2563eb',
                    borderRadius: 6,
                    barPercentage: 0.75
                },
                {
                    label: '👔 Pressing',
                    data: pressingData,
                    backgroundColor: '#06b6d4',
                    borderRadius: 6,
                    barPercentage: 0.75
                },
                {
                    label: '🚗 Lavage Auto',
                    data: carWashData,
                    backgroundColor: '#4f46e5',
                    borderRadius: 6,
                    barPercentage: 0.75
                },
                {
                    label: '🎟️ Billetterie Spectacles',
                    data: ticketsData,
                    backgroundColor: '#db2777',
                    borderRadius: 6,
                    barPercentage: 0.75
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        font: { weight: 'bold', size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' F CFA';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR').format(value) + ' F';
                        }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
