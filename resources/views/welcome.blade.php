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
