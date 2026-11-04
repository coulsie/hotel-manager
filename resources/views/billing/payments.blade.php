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

<div class="card shadow-sm border-dark">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="bi bi-journal-check me-2"></i>Toutes les transactions encaissées (Hôtel, Restaurant, Spectacles)
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
                                    // 🌟 CORRECTION ICI : On utilise duration_days au lieu de days
                                    $activeCard = \DB::table('magnetic_cards')->where('room_id', $billing->room_id)->latest()->first();
                                    $days = $activeCard ? ($activeCard->duration_days ?? 2) : 2;

                                    // Coût de l'hébergement (Ex: 75 000 × 5 = 375 000 F)
                                    $roomBaseCost = $billing->room->price_per_night * $days;

                                    // Commandes restaurant rattachées en option
                                    $restaurantOrdersTotal = \App\Models\Order::where('room_id', $billing->room_id)->sum('total_amount');

                                    // Calcul du Coût Global
                                    $totalCost = $roomBaseCost + $restaurantOrdersTotal;

                                    // Calcul du cumul de tous les paiements validés pour cette chambre
                                    $totalPaidForRoom = \App\Models\Billing::where('room_id', $billing->room_id)
                                        ->where('service_type', 'room_booking')
                                        ->where('status', 'completed')
                                        ->sum('amount_paid');

                                    // Reste à payer au franc près
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

                            <!-- Nouvelle Colonne : Coût de référence global de la note -->
                            <td class="text-end fw-bold text-dark">
                                {{ number_format($totalCost, 0, ',', ' ') }} F
                            </td>

                            <!-- Somme effectivement perçue sur CE reçu -->
                            <td class="text-end fw-bold text-success">
                                {{ number_format($billing->amount_paid, 0, ',', ' ') }} F
                            </td>

                            <!-- Colonne Reste à payer mise en conformité -->
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
                                <span class="fw-bold">Le grand livre des recettes est vierge.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Déclenchement automatique de la boîte de dialogue de l'imprimante -->
<script>
    window.addEventListener('DOMContentLoaded', (event) => {
        // Lance l'impression dès que la page est chargée
        window.print();

        // Optionnel : referme l'onglet automatiquement après impression ou annulation
        window.onafterprint = function() {
            window.close();
        };
    });
</script>

@endsection
