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
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>{{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif


@php
    $totalRoomsRemaining = collect($pendingRooms)->sum('remaining');
    $totalRoomsPaid      = collect($pendingRooms)->sum('amount_paid');
    $totalRestoPending   = $pendingOrders->sum('total_amount') + (isset($pendingExtraServices) ? $pendingExtraServices->sum('total_amount') : 0);
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
                            <td class="text-end">
                                <div class="fw-bold text-success fs-6">{{ number_format($pRoom['amount_paid'], 0, ',', ' ') }} F</div>
                                @if(!empty($pRoom['reservation_deposit']) && $pRoom['reservation_deposit'] > 0)
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        dont {{ number_format($pRoom['reservation_deposit'], 0, ',', ' ') }} F d'arrhes
                                    </div>
                                @endif
                            </td>
                            <td class="text-end fw-bold fs-6 {{ $pRoom['remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($pRoom['remaining'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-1 flex-wrap">
                                    @if($pRoom['remaining'] > 0)
                                        <!-- Tant qu'il reste un solde à payer : uniquement le bouton Solder -->
                                        <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-danger rounded-pill fw-bold px-3 shadow-sm">
                                            <i class="bi bi-receipt me-1"></i> Solder
                                        </a>
                                    @else
                                        <!-- Dès que le solde est à 0 : bouton Facture + bouton Check-out -->
                                        <a href="{{ route('billing.room', $pRoom['id']) }}" class="btn btn-sm btn-outline-success rounded-pill fw-bold px-2" title="Voir la facture">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <form action="{{ route('billing.room.checkout', $pRoom['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer le départ du client, clôturer la réservation et désactiver le badge de la Chambre {{ $pRoom['room_number'] }} ?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-dark rounded-pill fw-bold px-3 shadow-sm" title="Libérer la chambre maintenant">
                                                <i class="bi bi-door-open-fill text-warning me-1"></i> Check-out
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Bouton de prolongation de séjour toujours accessible -->
                                    <!-- Bouton d'ajustement de durée (+ ou - Nuit) -->
                                    <button type="button" class="btn btn-sm btn-info text-white rounded-pill fw-bold px-3 shadow-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#extendStayModal"
                                            data-room-id="{{ $pRoom['id'] }}"
                                            data-room-number="{{ $pRoom['room_number'] }}"
                                            data-current-days="{{ $pRoom['days'] }}"
                                            title="Prolonger ou réduire la durée du séjour">
                                        <i class="bi bi-calendar-range-fill me-1"></i> ± Nuit
                                    </button>

                                </div>

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
<!-- ===================================================================== -->
<!-- 3. SECTION B : ENCOURS RESTAURATION, PRESSING & LAVAGE AUTO           -->
<!-- ===================================================================== -->
<div class="card shadow border-0 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #b45309 0%, #f59e0b 100%);">
        <span><i class="bi bi-egg-fried me-2 fs-5"></i>Prestations Annexes & Restaurant non soldés</span>
        <span class="badge bg-white text-warning-emphasis rounded-pill px-3 py-1 fw-bold">
            {{ $pendingOrders->count() + (isset($pendingExtraServices) ? $pendingExtraServices->count() : 0) }} prestation(s)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #fffbeb; color: #92400e;">
                    <tr>
                        <th class="ps-4 py-3">Date / Heure</th>
                        <th class="py-3">Origine / Service</th>
                        <th class="py-3">Détail / Emplacement</th>
                        <th class="py-3">Affectation de la Note</th>
                        <th class="text-end py-3">Montant Dû</th>
                        <th class="text-center pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 1. COMMANDES RESTAURANT & BAR -->
                    @foreach($pendingOrders as $order)
                        <tr>
                            <td class="ps-4 small text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning rounded-pill px-3 py-1 fw-bold">
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
                    @endforeach

                    <!-- 2. PRESTATIONS PRESSING & LAVAGE AUTO (COULEURS DISTINCTES) -->
                    @if(isset($pendingExtraServices))
                        @foreach($pendingExtraServices as $srv)
                            <tr style="background-color: {{ $srv->category === 'pressing' ? '#f0fdfa' : '#eff6ff' }};">
                                <td class="ps-4 small text-muted">{{ $srv->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($srv->category === 'pressing')
                                        <span class="badge text-white rounded-pill px-3 py-1 fw-bold shadow-sm" style="background-color: #0891b2;">
                                            👔 PRESSING
                                        </span>
                                    @else
                                        <span class="badge text-white rounded-pill px-3 py-1 fw-bold shadow-sm" style="background-color: #4f46e5;">
                                            🚗 LAVAGE AUTO
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">
                                    {{ $srv->service_name }} <span class="badge bg-secondary-subtle text-dark ms-1">× {{ $srv->quantity }}</span>
                                </td>
                                <td>
                                    @if($srv->room)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-house-door-fill me-1"></i>Note Chambre ({{ $srv->room->number }})
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 fw-bold">
                                            <i class="bi bi-person-fill me-1"></i>Externe : {{ $srv->customer_name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-dark fs-6">{{ number_format($srv->total_amount, 0, ',', ' ') }} F</td>
                                <td class="text-center pe-4">
                                    <form action="{{ route('services.pay', $srv->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="cash">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold px-3 shadow-sm">
                                            <i class="bi bi-cash-coin me-1"></i> Encaisser Cash
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- MESSAGE SI AUCUN ENCOURS -->
                    @if($pendingOrders->isEmpty() && (!isset($pendingExtraServices) || $pendingExtraServices->isEmpty()))
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                <i class="bi bi-check-all text-success me-1"></i> Aucun encours de restauration, pressing ou lavage auto en attente.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- ===================================================================== -->
<!-- SECTION C : CONFÉRENCES, SÉMINAIRES & COWORKING NON SOLDÉS            -->
<!-- ===================================================================== -->
<div class="card shadow border-0 rounded-4 overflow-hidden mt-5">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center"
         style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);">
        <span><i class="bi bi-building-check text-warning me-2 fs-5"></i>Conférences, Séminaires & Coworking non soldés</span>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">
            {{ isset($pendingSpaceBookings) ? $pendingSpaceBookings->count() : 0 }} réservation(s)
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #eef2ff; color: #312e81;">
                    <tr>
                        <th class="ps-4 py-3">Date & Créneau</th>
                        <th class="py-3">Catégorie / Espace</th>
                        <th class="py-3">Détail & Capacité</th>
                        <th class="py-3">Affectation de la Note</th>
                        <th class="text-end py-3">Montant Dû</th>
                        <th class="text-center pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingSpaceBookings ?? [] as $sb)
                        @php
                            $remainingSpace = max(0, (float) $sb->total_amount - (float) $sb->amount_paid);
                            $catBadge = match($sb->space_category) {
                                'reunion'   => ['bg' => '#2563eb', 'label' => '🤝 RÉUNION'],
                                'seminaire' => ['bg' => '#7c3aed', 'label' => '🎤 SÉMINAIRE'],
                                'coworking' => ['bg' => '#059669', 'label' => '💻 COWORKING'],
                                default     => ['bg' => '#475569', 'label' => strtoupper($sb->space_category)],
                            };
                        @endphp
                        <tr>
                            <td class="ps-4 small">
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($sb->booking_date)->format('d/m/Y') }}</div>
                                <span class="badge bg-light text-primary border">
                                    {{ substr($sb->start_time, 0, 5) }} - {{ substr($sb->end_time, 0, 5) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge text-white rounded-pill px-3 py-1 fw-bold shadow-sm" style="background-color: {{ $catBadge['bg'] }};">
                                    {{ $catBadge['label'] }}
                                </span>
                                <div class="fw-bold text-dark mt-1 small">{{ $sb->space_name }}</div>
                            </td>
                            <td class="small">
                                <div class="fw-bold text-dark">{{ $sb->client_name }}</div>
                                <div class="text-muted">
                                    {{ $sb->attendees }} {{ $sb->space_category === 'coworking' ? 'poste(s)' : 'participant(s)' }}
                                    @if((float) $sb->amount_paid > 0)
                                        • <span class="text-success fw-semibold">Acompte : {{ number_format($sb->amount_paid, 0, ',', ' ') }} F</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($sb->room_id)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="bi bi-house-door-fill me-1"></i>Note Chambre ({{ $sb->room_number }})
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 fw-bold">
                                        <i class="bi bi-building me-1"></i>Externe / Société
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger fs-6">
                                {{ number_format($remainingSpace, 0, ',', ' ') }} F
                            </td>
                            <td class="text-center pe-4">
                                <form action="{{ route('conferences.bookings.status', $sb->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Encaisser le solde de {{ number_format($remainingSpace, 0, ',', ' ') }} F CFA pour {{ addslashes($sb->space_name) }} ?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="action" value="pay">
                                    <input type="hidden" name="payment_method" value="cash">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm">
                                        <i class="bi bi-cash-coin me-1"></i> Encaisser Cash
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                <i class="bi bi-check-all text-success me-1"></i> Aucun encours de conférence, séminaire ou coworking en attente.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modale de prolongation à coller tout en bas de billing/index.blade.php avant  -->
<!-- MODALE : PROLONGER OU RÉDUIRE LA DURÉE DU SÉJOUR -->
<div class="modal fade" id="extendStayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="extendStayForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-info text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-calendar-range-fill me-2"></i>Ajuster la durée — Chambre <span id="extendRoomNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-light border small mb-3 d-flex justify-content-between align-items-center">
                    <span>Durée actuelle du séjour :</span>
                    <strong class="fs-6 text-primary"><span id="currentStayDaysLabel">1</span> nuit(s)</strong>
                </div>

                <label class="form-label fw-bold small">Action & Nombre de nuitées *</label>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <select id="stayDirection" class="form-select fw-bold">
                            <option value="1" class="text-success">➕ Prolonger (+ Nuits)</option>
                            <option value="-1" class="text-danger">➖ Réduire (- Nuits)</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <input type="number" id="stayDaysCount" min="1" max="30" value="1" class="form-control fw-bold text-center" required>
                    </div>
                </div>

                <input type="hidden" name="extra_days" id="finalExtraDays" value="1">

                <div class="small text-muted bg-light p-2 rounded-3 border">
                    <i class="bi bi-info-circle text-info me-1"></i>
                    Nouvelle durée estimée : <strong id="previewNewDaysLabel" class="text-dark">2</strong> nuit(s)
                    <span class="d-block mt-1">La facture, le badge RFID et la date de sortie prévue seront recalculés automatiquement.</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold">Valider l'ajustement</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const extendModal = document.getElementById('extendStayModal');
        const dirSelect   = document.getElementById('stayDirection');
        const countInput  = document.getElementById('stayDaysCount');
        const hiddenInput = document.getElementById('finalExtraDays');
        const previewLbl  = document.getElementById('previewNewDaysLabel');
        let baseDays      = 1;

        function syncExtraDays() {
            const dir = parseInt(dirSelect.value || '1', 10);
            const cnt = Math.max(1, parseInt(countInput.value || '1', 10));
            const delta = dir * cnt;
            hiddenInput.value = delta;
            previewLbl.textContent = Math.max(1, baseDays + delta);
        }

        if (extendModal) {
            extendModal.addEventListener('show.bs.modal', function (event) {
                const btn     = event.relatedTarget;
                const roomId  = btn.getAttribute('data-room-id');
                const roomNum = btn.getAttribute('data-room-number');
                baseDays      = parseInt(btn.getAttribute('data-current-days') || '1', 10);

                document.getElementById('extendRoomNumber').textContent = roomNum;
                document.getElementById('currentStayDaysLabel').textContent = baseDays;

                // Conservez ici votre route exacte (ex: /rooms/${roomId}/extend ou /checkin/extend/${roomId})
                document.getElementById('extendStayForm').action = `/rooms/${roomId}/extend`;

                dirSelect.value  = '1';
                countInput.value = '1';
                syncExtraDays();
            });

            dirSelect.addEventListener('change', syncExtraDays);
            countInput.addEventListener('input', syncExtraDays);
        }
    });
</script>

@endsection
