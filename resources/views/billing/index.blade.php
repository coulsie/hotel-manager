@extends('layouts.app')

@section('title', 'Encours & Factures - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 fw-bold text-dark">
        <i class="bi bi-calculator text-primary me-2"></i>Tableau de Bord des Encours Clients
    </h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- SECTION A : SUIVI DE TOUTES LES CHAMBRES OCCUPÉES (HÉBERGEMENT & EXTRAS) -->
<div class="card shadow-sm border-danger mb-4 rounded-4 overflow-hidden">
    <div class="card-header bg-danger text-white fw-bold py-3">
        <i class="bi bi-house-door me-2"></i>Comptes Hébergement en cours d'occupation
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3">Chambre</th>
                        <th>Arrivée</th>
                        <th>Sortie Prévue</th>
                        <th class="text-end">Hébergement</th>
                        <th class="text-end">Extras (Resto)</th>
                        <th class="text-end">Total Séjour</th>
                        <th class="text-end">Déjà Payé</th>
                        <th class="text-end text-danger">Reste à Payer</th>
                        <th class="text-center pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRooms as $pRoom)
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-primary">Chambre {{ $pRoom['room_number'] }}</div>
                                <span class="badge bg-secondary" style="font-size: 0.7rem;">
                                    {{ strtoupper($pRoom['room_type']) }} ({{ $pRoom['days'] }}j)
                                </span>
                            </td>
                            <td class="small">{{ $pRoom['created_at']->format('d/m/Y H:i') }}</td>
                            <td class="small fw-semibold text-dark">
                                <i class="bi bi-calendar-check text-primary me-1"></i>{{ $pRoom['checkout_date']->format('d/m/Y H:i') }}
                            </td>
                            <td class="text-end small fw-semibold">{{ number_format($pRoom['room_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end small fw-bold text-warning-emphasis">
                                {{ number_format($pRoom['extras_cost'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-bold text-dark">{{ number_format($pRoom['total_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold text-success">{{ number_format($pRoom['amount_paid'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold {{ $pRoom['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($pRoom['remaining'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center pe-3">
                                @if($pRoom['remaining'] > 0)
                                    <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3">
                                        <i class="bi bi-receipt me-1"></i> Clôturer & Solder
                                    </a>
                                @else
                                    <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-success rounded-pill fw-bold px-3">
                                        <i class="bi bi-check-circle me-1"></i> Soldé (Facture)
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

<!-- SECTION B : ENCOURS RESTAURATION & BAR COMPTOIR -->
<div class="card shadow-sm border-warning rounded-4 overflow-hidden">
    <div class="card-header bg-warning text-dark fw-bold py-3">
        <i class="bi bi-egg-fried me-2"></i>Commandes Restaurant & Bar Piscine non soldées
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="ps-3">Date / Heure</th>
                        <th>Origine / Service</th>
                        <th>Emplacement original</th>
                        <th>Affectation de la Note</th>
                        <th class="text-end">Montant Dû</th>
                        <th class="text-center pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingOrders as $order)
                        <tr>
                            <td class="ps-3 small">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-info text-dark">🍽️ RESTAURANT / BAR</span></td>
                            <td class="fw-semibold">{{ $order->restaurantTable->number ?? 'Comptoir Direct' }}</td>
                            <td>
                                @if($order->room)
                                    <span class="text-danger fw-bold"><i class="bi bi-house-door me-1"></i>Note Chambre ({{ $order->room->number }})</span>
                                @else
                                    <span class="text-success fw-bold"><i class="bi bi-wallet2 me-1"></i>Client Comptoir (Cash)</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-dark">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                            <td class="text-center pe-3">
                                <form action="{{ route('billing.restaurant.pay', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="payment_method" value="cash">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold px-3">
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
