@extends('layouts.app')

@section('title', 'Facture Chambre ' . $room->number . ' - Hotel Manager')

@section('content')
<!-- Boutons d'actions en haut (Masqués à l'impression) -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom d-print-none">
    <h1 class="h2 text-dark"><i class="bi bi-file-earmark-text text-primary me-2"></i>Facture Clôturée : Chambre {{ $room->number }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Retour aux encours
        </a>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Imprimer
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 d-print-none" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- LE REÇU / FACTURE PHYSIQUE -->
<div class="invoice-box p-5 bg-white shadow rounded-4 border">

    <!-- Entête de la facture -->
    <div class="row mb-5">
        <div class="col-6">
            <h3 class="fw-black text-primary mb-1"><i class="bi bi-building-fill me-2"></i>HOTEL MANAGER RESORT</h3>
            <p class="text-muted small mb-0">
                Boulevard de la Marina, Abidjan, Côte d'Ivoire<br>
                Tél: +225 07 00 00 00 00 | Email: contact@hotelmanager.com
            </p>
        </div>
        <div class="col-6 text-end">
            <h2 class="fw-bold text-uppercase text-secondary mb-1" style="letter-spacing: 1px;">Facture</h2>
            <div class="text-dark fw-bold mb-1">N° FAC-{{ date('Ymd') }}-{{ $room->id }}</div>
            <div class="text-muted small">Date d'émission : {{ date('d/m/Y à H:i') }}</div>
        </div>
    </div>

    <!-- Informations Client & Chambre -->
    <div class="row p-4 bg-light rounded-4 border mb-5">
        <div class="col-6 border-end">
            <h6 class="text-muted text-uppercase fw-bold small mb-2">Détails de l'Hébergement</h6>
            <div class="fs-6 fw-bold text-dark">Chambre : <span class="text-primary">{{ $room->number }}</span></div>
            <div class="text-secondary small mt-1">Catégorie : <span class="badge bg-secondary">{{ strtoupper($room->type) }}</span></div>
            <div class="text-secondary small mt-1">Prix unitaire nuitée : {{ number_format($room->price_per_night, 0, ',', ' ') }} F CFA</div>
        </div>
        <div class="col-6 ps-4">
            <h6 class="text-muted text-uppercase fw-bold small mb-2">Facturé à</h6>
            <div class="fs-6 fw-bold text-dark">{{ $room->currentGuest->name ?? 'Client Passager / Client de la Chambre' }}</div>
            <div class="text-secondary small mt-1">Date d'arrivée : {{ $room->currentCheckin->checkin_date ?? date('d/m/Y') }}</div>
            <div class="text-secondary small mt-1">
                Statut du règlement :
                @if(isset($isPaid) && $isPaid)
                    <span class="badge bg-success rounded-pill px-3 py-1.5"><i class="bi bi-check-all me-1"></i> PAYÉ & CLÔTURÉ</span>
                @else
                    <span class="badge bg-danger rounded-pill px-3 py-1.5"><i class="bi bi-exclamation-triangle me-1"></i> En attente de paiement</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tableau détaillé des consommations -->
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-list-ul text-primary me-2"></i>Détail des Prestations consommées</h5>
    <div class="table-responsive mb-5">
        <table class="table table-bordered align-middle">
            <thead class="table-light text-uppercase small" style="font-size: 0.75rem;">
                <tr>
                    <th class="py-3 ps-3">Désignation de la prestation / Service</th>
                    <th class="text-center py-3" style="width: 15%;">Quantité / Nuits</th>
                    <th class="text-end py-3" style="width: 20%;">Prix Unitaire</th>
                    <th class="text-end py-3 pe-3" style="width: 25%;">Total (F CFA)</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. BLOC LOGEMENT (NUITÉES) -->
                @php
                    $nights = $room->currentCheckin ? now()->diffInDays(\Carbon\Carbon::parse($room->currentCheckin->checkin_date)) : 1;
                    if($nights == 0) $nights = 1;
                    $total_lodging = $room->price_per_night * $nights;
                    $grand_total = $total_lodging;
                @endphp
                <tr>
                    <td class="ps-3 fw-bold text-dark">
                        <i class="bi bi-moon-stars me-2 text-indigo"></i>Frais de Logement / Nuitées hôtelières
                    </td>
                    <td class="text-center fw-bold">{{ $nights }}</td>
                    <td class="text-end">{{ number_format($room->price_per_night, 0, ',', ' ') }} F</td>
                    <td class="text-end pe-3 fw-bold text-primary">{{ number_format($total_lodging, 0, ',', ' ') }} F</td>
                </tr>

                <!-- 2. BLOC RESTAURATION -->
               <!-- 2. BLOC RESTAURATION (UNIQUEMENT LES COMMANDES NON PAYÉES) -->
                @if(isset($room->orders) && $room->orders->count() > 0)
                    @foreach($room->orders as $order)
                        {{-- 🛑 CONDITION : Si la commande est déjà payée en cash, on l'ignore et on ne l'affiche pas sur la chambre --}}
                        @if($order->status !== 'paid')
                            @php $grand_total += $order->total_amount; @endphp
                            <tr>
                                <td class="ps-3 text-dark">
                                    <i class="bi bi-egg-fried me-2 text-warning"></i>Restauration - Commande N°#{{ $order->id }}
                                </td>
                                <td class="text-center">1</td>
                                <td class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                                <td class="text-end pe-3 fw-bold text-dark">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                            </tr>
                        @endif
                    @endforeach
                @endif


                <!-- 3. BLOC BILLETTERIE -->
                @if(isset($room->bookings) && $room->bookings->count() > 0)
                    @foreach($room->bookings as $booking)
                        @php $grand_total += $booking->amount_paid; @endphp
                        <tr>
                            <td class="ps-3 text-dark">
                                <i class="bi bi-ticket-perforated me-2 text-danger"></i>Billet Concert : "{{ $booking->event->title ?? 'Spectacle' }}" (Catégorie: {{ strtoupper($booking->ticket_type) }})
                            </td>
                            <td class="text-center">1</td>
                            <td class="text-end">{{ number_format($booking->amount_paid, 0, ',', ' ') }} F</td>
                            <td class="text-end pe-3 fw-bold text-dark">{{ number_format($booking->amount_paid, 0, ',', ' ') }} F</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Bloc de calcul final et validation du paiement -->
    <div class="row align-items-center">
        <!-- Zone Validation Paiement (Masquée si déjà payé ou à l'impression) -->
        <div class="col-md-7 d-print-none">
            @if(!isset($isPaid) || !$isPaid)
                <div class="card border-danger shadow-sm rounded-4 p-3 bg-light">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-cash-coin me-2"></i>Encaisser le règlement au comptoir</h6>
                    <form action="{{ route('billing.room.pay', $room->id) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-sm-7">
                                <select class="form-select fw-bold text-dark" name="payment_method" required>
                                    <option value="cash" selected>💵 Paiement en Espèces (Cash)</option>
                                    <option value="card">💳 Carte Bancaire / Terminal POS</option>
                                    <option value="mobile_money">📱 Mobile Money (Orange)</option>
                                    <option value="mobile_money">📱 Mobile Money (MTN)</option>
                                    <option value="mobile_money">📱 Mobile Money (Moov)</option>
                                    <option value="mobile_money">📱 Mobile Money (Wave)</option>
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i> Valider & Imprimer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Bloc Solde Finaux -->
               <!-- Bloc Solde Finaux -->
        <div class="col-md-5 ms-auto">
            <div class="p-4 rounded-4 bg-light border shadow-sm">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary fw-semibold">Sous-Total Hors Taxes :</span>
                    <span class="text-dark fw-bold">{{ number_format($grand_total, 0, ',', ' ') }} F</span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-secondary fw-semibold">TVA (0% / Exonéré) :</span>
                    <span class="text-dark fw-bold">0 F</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-dark fw-black text-uppercase small">Net à percevoir :</span>
                    <span class="fs-4 fw-black text-success">{{ number_format($grand_total, 0, ',', ' ') }} F CFA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pied de page de la facture -->
    <div class="text-center mt-5 pt-4 border-top text-muted small">
        Merci pour votre séjour dans notre établissement !<br>
        <span class="fw-bold">Hotel Manager System v13.39</span>
    </div>
</div>

<!-- STYLE DE SÉCURITÉ POUR L'IMPRESSION PROPRE SANS LES MENUS DU SITE -->
<style>
@media print {
    body {
        background-color: #ffffff !important;
        padding-top: 0 !important;
    }
    .sidebar, header, .navbar, .btn, .d-print-none, .alert, .card {
        display: none !important;
    }
    main, .col-md-9, .col-lg-10 {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .main-card-container {
        box-shadow: none !important;
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }
    .invoice-box {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}
</style>

<!-- ENCLENCHEMENT DE L'IMPRESSION AUTOMATIQUE APRES ENCAISSEMENT REUSSI -->
@if(session('success'))
<script>
    window.addEventListener('load', function() {
        window.print();
    });
</script>
@endif

@endsection
