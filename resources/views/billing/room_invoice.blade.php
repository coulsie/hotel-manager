@extends('layouts.app')

@section('title', 'Facture ' . $room->number)

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-receipt me-2"></i>Détail Facture Clôturée - {{ $room->number }}</h1>
    <button class="btn btn-outline-dark" onclick="window.print()"><i class="bi bi-printer me-2"></i>Imprimer le Reçu Global</button>
</div>

<div class="row">
    <!-- Détail des postes de dépenses -->
    <div class="col-md-8 mb-4">
        <!-- Hébergement -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold"><i class="bi bi-house-door me-2"></i>Frais d'Hébergement de base</div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Frais de séjour par défaut (Simulation nuitée encours)</span>
                    <span class="fw-bold">{{ number_format($roomCost, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>

        <!-- Consommations Bar & Restaurant -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold"><i class="bi bi-egg-fried me-2"></i>Consommations Restaurant & Bar Piscine</div>
            <div class="card-body p-0">
                <table class="table mb-0 table-sm">
                    <thead>
                        <tr class="table-secondary">
                            <th class="ps-3">N° Commande</th>
                            <th>Emplacement</th>
                            <th>Date</th>
                            <th class="text-end pe-3">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-3">#{{ $order->id }}</td>
                                <td>{{ $order->restaurantTable->number ?? 'Comptoir' }}</td>
                                <td>{{ $order->created_at->format('d/m/H:i') }}</td>
                                <td class="text-end pe-3 fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">Aucune note de restauration enregistrée sur cette chambre.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Résumé financier de la Facture -->
    <div class="col-md-4">
        <div class="card shadow-sm bg-dark text-white">
            <div class="card-header border-secondary"><h5 class="mb-0 fw-bold">Résumé Compte Client</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Hébergement :</span>
                    <span>{{ number_format($roomCost, 0, ',', ' ') }} F</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Restauration :</span>
                    <span>{{ number_format($restaurantTotal, 0, ',', ' ') }} F</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Culture/Shows :</span>
                    <span>{{ number_format($showsTotal, 0, ',', ' ') }} F</span>
                </div>
                <hr class="border-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-bold text-warning">NET À PAYER :</span>
                    <span class="fs-4 fw-bold text-warning">{{ number_format($totalInvoice, 0, ',', ' ') }} F</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
