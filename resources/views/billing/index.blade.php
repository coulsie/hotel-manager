@extends('layouts.app')

@section('title', 'Encours & Factures - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-calculator-fill text-primary me-2"></i>Tableau de Bord des Encours Clients
        </h1>
        <p class="text-muted small mb-0 mt-1">Suivi en temps réel des soldes d'hébergement et des consommations restaurant en attente</p>
    </div>
    <a href="{{ route('billing.payments') }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-journal-check text-warning me-1"></i> Grand Livre des Recettes
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $totalRoomsRemaining = collect($pendingRooms)->sum('remaining');
    $totalRoomsPaid      = collect($pendingRooms)->sum('amount_paid');
    $totalRestoPending   = $pendingOrders->sum('total_amount');
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DES ENCOURS (DÉGRADÉS ÉCLATANTS)                -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Reste à solder Chambres -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Solde Chambres Dû</span>
                    <i class="bi bi-house-exclamation-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($totalRoomsRemaining, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Commandes Restaurant en attente -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Encours Restaurant</span>
                    <i class="bi bi-cup-hot-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($totalRestoPending, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Acomptes déjà perçus sur séjours -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Déjà Versé (Séjours)</span>
                    <i class="bi bi-check-circle-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($totalRoomsPaid, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 4 : Chambres actuellement occupées -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Chambres Occupées</span>
                    <i class="bi bi-door-closed-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ count($pendingRooms) }} <small class="fs-6 fw-normal">en cours</small></h3>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. SECTION A : SUIVI DES CHAMBRES OCCUPÉES (DÉGRADÉ RUBIS / CORAIL)   -->
<!-- ===================================================================== -->
<div class="card shadow border-0 mb-5 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
        <span><i class="bi bi-house-door-fill text-warning me-2 fs-5"></i>Comptes Hébergement en cours d'occupation</span>
        <span class="badge bg-white text-danger rounded-pill px-3 py-1 fw-bold">{{ count($pendingRooms) }} chambre(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #fef2f2; color: #991b1b;">
                    <tr>
                        <th class="ps-4 py-3">Chambre</th>
                        <th class="py-3">Arrivée</th>
                        <th class="py-3">Sortie Prévue</th>
                        <th class="text-end py-3">Hébergement</th>
                        <th class="text-end py-3">Extras (Resto)</th>
                        <th class="text-end py-3">Total Séjour</th>
                        <th class="text-end py-3 text-success">Déjà Payé</th>
                        <th class="text-end py-3 text-danger">Reste à Payer</th>
                        <th class="text-center pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRooms as $pRoom)
                        <tr {!! $pRoom['remaining'] > 0 ? 'style="background-color: #fffafa;"' : '' !!}>
                            <td class="ps-4">
                                <div class="fw-bold text-primary fs-6">🚪 Chambre {{ $pRoom['room_number'] }}</div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 0.7rem;">
                                    {{ strtoupper($pRoom['room_type']) }} • {{ $pRoom['days'] }}j
                                </span>
                            </td>
                            <td class="small text-muted">{{ $pRoom['created_at']->format('d/m/Y H:i') }}</td>
                            <td class="small fw-bold text-dark">
                                <span class="badge bg-light text-dark border px-2 py-1">
                                    <i class="bi bi-calendar-check text-primary me-1"></i>{{ $pRoom['checkout_date']->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="text-end small fw-semibold">{{ number_format($pRoom['room_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end small fw-bold text-warning-emphasis">
                                {{ number_format($pRoom['extras_cost'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">{{ number_format($pRoom['total_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold text-success fs-6">{{ number_format($pRoom['amount_paid'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold fs-6 {{ $pRoom['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($pRoom['remaining'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center pe-4">
                                @if($pRoom['remaining'] > 0)
                                    <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 shadow-sm">
                                        <i class="bi bi-receipt me-1"></i> Solder
                                    </a>
                                @else
                                    <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-success rounded-pill fw-bold px-3 shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i> Soldé (Voir)
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">
                                <i class="bi bi-shield-check text-success me-1"></i> Aucune chambre occupée actuellement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 3. SECTION B : ENCOURS RESTAURATION & BAR (DÉGRADÉ AMBRE / ORANGE)    -->
<!-- ===================================================================== -->
<div class="card shadow border-0 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);">
        <span><i class="bi bi-egg-fried me-2 fs-5"></i>Commandes Restaurant & Bar Piscine non soldées</span>
        <span class="badge bg-white text-warning-emphasis rounded-pill px-3 py-1 fw-bold">{{ $pendingOrders->count() }} commande(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #fffbeb; color: #92400e;">
                    <tr>
                        <th class="ps-4 py-3">Date / Heure</th>
                        <th class="py-3">Origine / Service</th>
                        <th class="py-3">Emplacement original</th>
                        <th class="py-3">Affectation de la Note</th>
                        <th class="text-end py-3">Montant Dû</th>
                        <th class="text-center pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingOrders as $order)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 fw-bold">
                                    🍽️ RESTAURANT / BAR
                                </span>
                            </td>
                            <td class="fw-bold text-dark">{{ $order->restaurantTable->number ?? 'Comptoir Direct' }}</td>
                            <td>
                                @if($order->room)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="bi bi-house-door-fill me-1"></i>Note Chambre ({{ $order->room->number }})
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="bi bi-wallet2 me-1"></i>Client Comptoir (Cash)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark fs-6">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                            <td class="text-center pe-4">
                                <form action="{{ route('billing.restaurant.pay', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="cash">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm">
                                        <i class="bi bi-currency-exchange me-1"></i> Encaisser Cash
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                <i class="bi bi-check-all text-success me-1"></i> Aucun encours de restauration en attente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
