@extends('layouts.app')

@section('title', 'Grand Livre des Recettes - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cash-coin me-2"></i>Grand Livre des Recettes (Historique)</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- 🔍 SECTION FILTRES & RECHERCHE -->
<div class="card shadow-sm border-secondary mb-4">
    <div class="card-body bg-light">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
            <!-- Période temporelle -->
            <div class="col-md-3">
                <label for="period" class="form-label fw-bold small text-secondary">Période fiscale :</label>
                <select class="form-select" name="period" id="period" onchange="this.form.submit()">
                    <option value="" {{ request('period') == '' ? 'selected' : '' }}>🔄 Toutes les transactions</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>📅 Aujourd'hui (Journalier)</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>📊 Cette semaine (Hebdomadaire)</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>🗓️ Ce mois (Mensuel)</option>
                </select>
            </div>

            <!-- Recherche textuelle -->
            <div class="col-md-6">
                <label for="search" class="form-label fw-bold small text-secondary">Rechercher un reçu, une chambre ou un mode :</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="search" id="search" placeholder="Ex: REC-2026..., Chambre 102, cash, wave..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold">
                    <i class="bi bi-filter me-1"></i> Filtrer
                </button>
                @if(request('period') || request('search'))
                    <a href="{{ url()->current() }}" class="btn btn-outline-danger" title="Réinitialiser">
                        <i class="bi bi-x-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-dark">
    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-check me-2"></i>Toutes les transactions encaissées (Hôtel, Restaurant, Spectacles)</span>
        @if(method_exists($billings, 'total'))
            <span class="badge bg-secondary">{{ $billings->total() }} transaction(s)</span>
        @endif
    </div>


    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-secondary">
                    <tr>
                        <th>N° Reçu</th>
                        <th>Date & Heure</th>
                        <th>Type de Flux</th>
                        <th>Détails de l'origine</th>
                        <th>Mode de Règlement</th>
                        <th class="text-end">Coût Total</th>
                        <th class="text-end text-success">Somme Perçue</th>
                        <th class="text-end text-danger">Reste à payer</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billings as $billing)
                        @php
                                $totalCost = 0;
                                $remaining = 0;

                                if($billing->service_type === 'room_booking' && $billing->room) {
                                    $activeCard = \DB::table('magnetic_cards')->where('room_id', $billing->room_id)->latest()->first();
                                    $days = $activeCard ? ($activeCard->duration_days ?? 2) : 2;
                                    $roomBaseCost = $billing->room->price_per_night * $days;

                                    $restaurantOrdersTotal = \App\Models\Order::where('room_id', $billing->room_id)->sum('total_amount');
                                    $totalCost = $roomBaseCost + $restaurantOrdersTotal;

                                    $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)
                                        ->where('service_type', 'room_booking')
                                        ->where('status', 'completed')
                                        ->sum('amount_paid');

                                    $remaining = $totalCost - $totalPaidForRoom;
                                    if($remaining < 0) $remaining = 0;

                                } elseif($billing->service_type === 'restaurant' && $billing->order) {
                                    $totalCost = $billing->order->total_amount;
                                    $remaining = 0;
                                } elseif($billing->service_type === 'show_ticket' && $billing->eventTicket) {
                                    $totalCost = $billing->eventTicket->price;
                                    $remaining = 0;
                                } else {
                                    $totalCost = $billing->amount_paid;
                                    $remaining = 0;
                                }
                            @endphp
                        <tr>
                            <td class="fw-bold text-primary">{{ $billing->receipt_number }}</td>
                            <td>{{ $billing->created_at ? $billing->created_at->format('d/m/Y H:i') : '---' }}</td>
                            <td>
                                @if($billing->service_type === 'restaurant')
                                    <span class="badge bg-info text-dark">🍽️ RESTAURANT</span>
                                @elseif($billing->service_type === 'room_booking')
                                    <span class="badge bg-danger">🏨 HÔTEL (CHAMBRE)</span>
                                @elseif($billing->service_type === 'show_ticket')
                                    <span class="badge bg-purple text-white" style="background-color: #6f42c1;">🎟️ SPECTACLE</span>
                                @endif
                            </td>
                            <td>
                                @if($billing->service_type === 'restaurant' && $billing->order)
                                    Table n° {{ $billing->order->restaurantTable->number ?? 'Comptoir' }}
                                @elseif($billing->service_type === 'room_booking' && $billing->room)
                                    Chambre {{ $billing->room->number }}
                                @elseif($billing->service_type === 'show_ticket' && $billing->eventTicket && $billing->eventTicket->event)
                                    {{ $billing->eventTicket->event->title }} ({{ ucfirst($billing->eventTicket->ticket_type) }})
                                @else
                                    <span class="text-muted small">Poste clôturé</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-secondary text-uppercase">
                                    {{ str_replace('_', ' ', $billing->payment_method) }}
                                </span>
                            </td>

                            <td class="text-end fw-bold text-dark">
                                {{ number_format($totalCost, 0, ',', ' ') }} F
                            </td>

                            <td class="text-end fw-bold text-success">
                                {{ number_format($billing->amount_paid, 0, ',', ' ') }} F
                            </td>

                            <td class="text-end fw-bold {{ $remaining > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($remaining, 0, ',', ' ') }} F
                            </td>

                            <td class="text-center">
                                <a href="{{ route('billing.print', $billing->id) }}" target="_blank" class="btn btn-sm btn-primary" title="Imprimer le reçu de caisse">
                                    <i class="bi bi-printer-fill me-1"></i> Imprimer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-4 d-block mb-2 text-secondary"></i>
                                <span class="fw-bold">Aucune recette ne correspond à vos critères de recherche.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
              <!-- 💰 TOTAL DES RECETTES DE LA PÉRIODE -->
                <div class="row mb-4">
                    <div class="col-md-4 ms-auto">
                        <div class="card shadow-sm border-success bg-success bg-opacity-10 text-success text-end">
                            <div class="card-body py-2 px-3">
                                <span class="text-uppercase small fw-bold d-block text-secondary">Recettes cumulées :</span>
                                <span class="fs-3 fw-bold">{{ number_format($periodTotal, 0, ',', ' ') }} F CFA</span>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Liens de pagination si activés côté serveur -->
@if(method_exists($billings, 'links'))
    <div class="mt-3 d-flex justify-content-center">
        {{ $billings->appends(request()->query())->links() }}
@endif
@endsection
