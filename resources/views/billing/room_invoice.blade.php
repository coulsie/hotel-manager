@extends('layouts.app')

@section('title', 'Facture ' . $room->number)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-receipt me-2"></i>Détail Facture Chambre {{ $room->number }}</h1>
    <button class="btn btn-outline-dark" onclick="window.print()"><i class="bi bi-printer me-2"></i>Imprimer cette vue</button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Détail des postes de dépenses -->
    <div class="col-md-8 mb-4">
        <!-- Hébergement -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-house-door me-2"></i>Frais d'Hébergement (Séjour actif)
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">Hébergement Chambre {{ $room->number }}</span>
                        <small class="text-muted d-block">{{ $days }} nuit(s) occupée(s) × {{ number_format($room->price_per_night, 0, ',', ' ') }} F</small>
                    </div>
                    <span class="fw-bold fs-5 text-dark">{{ number_format($roomCost, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>

        <!-- Consommations Bar & Restaurant -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">
                <i class="bi bi-egg-fried me-2"></i>Consommations Restaurant & Bar Piscine
            </div>
            <div class="card-body p-0">
                <table class="table mb-0 table-sm align-middle">
                    <thead>
                        <tr class="table-secondary">
                            <th class="ps-3 py-2">N° Commande</th>
                            <th>Emplacement</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-3 py-2">#{{ $order->id }}</td>
                                <td>{{ $order->restaurantTable->number ?? 'Comptoir Direct' }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted small">Aucune note de restauration enregistrée sur cette chambre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé financier & Formulaire d'encaissement -->
    <div class="col-md-4">
        <div class="card shadow-sm bg-dark text-white mb-4">
            <div class="card-header border-secondary">
                <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2 text-warning"></i>Résumé Compte Client</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2 small text-light">
                    <span>Total Hébergement :</span>
                    <span>{{ number_format($roomCost, 0, ',', ' ') }} F</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-light">
                    <span>Total Restauration :</span>
                    <span>{{ number_format($restaurantTotal, 0, ',', ' ') }} F</span>
                </div>
                <div class="d-flex justify-content-between mb-3 small text-light">
                    <span>Total Culture/Shows :</span>
                    <span>{{ number_format($showsTotal, 0, ',', ' ') }} F</span>
                </div>

                <hr class="border-secondary my-2">

                <!-- 1. Le Total brut global -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-light">MONTANT TOTAL DÛ :</span>
                    <span class="fw-bold text-light fs-5">{{ number_format($totalInvoice, 0, ',', ' ') }} F</span>
                </div>

                <!-- 2. L'acompte déjà versé -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-success">ACOMPTE DÉJÀ PAYÉ :</span>
                    <span class="fw-bold text-success fs-5">- {{ number_format($alreadyPaid, 0, ',', ' ') }} F</span>
                </div>

                <hr class="border-secondary my-2">

                <!-- 3. Reste à solder dynamique -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-5 fw-bold text-warning">RESTE À PAYER :</span>
                    <span class="fs-4 fw-bold text-warning">{{ number_format($remainingToPay, 0, ',', ' ') }} F</span>
                </div>

                <!-- Formulaire de Validation du Règlement -->
                <form action="{{ route('billing.room.pay', $room->id) }}" method="POST" class="bg-secondary p-3 rounded-3 text-dark">
                    @csrf

                    <!-- Correction : Le montant envoyé correspond au reste à payer réel -->
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label text-white fw-bold small">Montant à encaisser (F CFA) :</label>
                        <input type="number" class="form-control form-control-lg fw-bold text-end text-danger" name="amount_paid" id="amount_paid" value="{{ $remainingToPay }}" min="0" max="{{ $remainingToPay }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white fw-bold small">Mode de règlement :</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">💵 Espèces / Cash</option>
                            <option value="wave">🧡 Wave</option>
                            <option value="orange_money">🍊 Orange Money</option>
                            <option value="mtn_momo">💛 MTN MoMo</option>
                            <option value="card">💳 Carte Bancaire</option>
                        </select>
                    </div>

                    @if($remainingToPay <= 0)
                        <!-- Bloc d'information si la facture est déjà soldée -->
                        <div class="alert alert-success text-center fw-bold py-2 mb-0" role="alert">
                            <i class="bi bi-shield-check me-1"></i> Facture entièrement réglée
                        </div>
                    @else
                        <button type="submit" class="btn btn-warning w-100 fw-bold">
                            <i class="bi bi-check2-square me-1"></i> Clôturer & Encaisser le Solde
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
