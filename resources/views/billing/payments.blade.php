@extends('layouts.app')

@section('title', 'Historique des Paiements - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cash-coin me-2"></i>Registre Global de Tous les Paiements</h1>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Date de l'opération</th>
                        <th>Type de service</th>
                        <th>Emplacement original</th>
                        <th>Imputation / Affectation</th>
                        <th class="text-end">Somme Perçue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($restaurantOrders as $order)
                        <tr>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="badge bg-{{ $order->restaurantTable && $order->restaurantTable->location_type === 'restaurant' ? 'info' : 'warning' }} text-dark">
                                    {{ $order->restaurantTable ? strtoupper($order->restaurantTable->location_type) : 'BAR' }}
                                </span>
                            </td>
                            <td>{{ $order->restaurantTable->number ?? 'Comptoir Direct' }}</td>
                            <td>
                                @if($order->room)
                                    <span class="text-danger fw-bold"><i class="bi bi-house-door me-1"></i>Note Chambre ({{ $order->room->number }})</span>
                                @else
                                    <span class="text-success fw-bold"><i class="bi bi-wallet2 me-1"></i>Payé cash au comptoir</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-success">{{ number_format($order->total_amount, 0, ',', ' ') }} F</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
