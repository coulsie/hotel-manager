@extends('layouts.app')

@section('title', 'État des Ventes - ' . $event->title)

@section('content')
<!-- En-tête -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 text-dark"><i class="bi bi-graph-up-arrow text-success me-2"></i>Rapport des Ventes : {{ $event->title }}</h1>
    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
        <i class="bi bi-arrow-left me-1"></i> Retour aux grilles
    </a>
</div>

<div class="row mb-4">
    <!-- Carte : Infos Salle -->
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Infrastructures</h6>
            <div class="fs-5 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i>{{ $event->venue->title ?? 'Salle non définie' }}</div>
            <div class="text-secondary small mt-1">Capacité maximale de sécurité : <strong>{{ number_format($event->venue->total_capacity ?? 0, 0, ',', ' ') }} places</strong></div>
        </div>
    </div>
    <!-- Carte : Date -->
    <div class="col-md-6 mb-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Planification</h6>
            <div class="fs-5 fw-bold text-dark"><i class="bi bi-calendar-event me-2 text-danger"></i>{{ \Carbon\Carbon::parse($event->performance_date)->format('d/m/Y à H\hi') }}</div>
            <div class="text-secondary small mt-1">Statut : <span class="badge bg-success rounded-pill">Programmé</span></div>
        </div>
    </div>
</div>

<!-- LE TABLEAU DES PLACES RESTANTES PAR CATÉGORIE -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-tags me-2 text-warning"></i>Jauge et Remplissage par Catégorie de Billet</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-uppercase small" style="font-size: 0.8rem;">
                <tr>
                    <th class="ps-4 py-3">Catégorie</th>
                    <th class="py-3">Prix Unitaire</th>
                    <th class="text-center py-3">Places Initiales</th>
                    <th class="text-center py-3">Places Vendues</th>
                    <th class="text-center py-3">Places Restantes</th>
                    <th class="py-3" style="width: 20%;">Taux d'occupation</th>
                    <th class="text-end pe-4 py-3">Chiffre d'Affaires</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCA = 0;
                    $totalVendues = 0;
                    $totalInitiales = 0;
                @endphp
                @foreach($event->tickets as $ticket)
                    @php
                        $vendues = $ticket->total_capacity - $ticket->available_seats;
                        $caTarif = $vendues * $ticket->price;
                        $totalCA += $caTarif;
                        $totalVendues += $vendues;
                        $totalInitiales += $ticket->total_capacity;

                        // Calcul du pourcentage de remplissage
                        $percent = $ticket->total_capacity > 0 ? round(($vendues / $ticket->total_capacity) * 100) : 0;
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-dark text-uppercase">
                            @if($ticket->ticket_type === 'vip') 💎 VIP @elseif($ticket->ticket_type === 'honneurs') 🌟 Honneur @else 🎟️ {{ $ticket->ticket_type }} @endif
                        </td>
                        <td class="fw-bold text-secondary">{{ number_format($ticket->price, 0, ',', ' ') }} F</td>
                        <td class="text-center fw-bold">{{ $ticket->total_capacity }}</td>
                        <td class="text-center text-danger fw-bold">{{ $vendues }}</td>
                        <td class="text-center text-success fw-bold fs-6 bg-light-success">{{ $ticket->available_seats }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress w-100" style="height: 8px;">
                                    <div class="progress-bar @if($percent > 80) bg-danger @elseif($percent > 50) bg-warning @else bg-success @endif" role="progressbar" style="width: {{ $percent }}%"></div>
                                </div>
                                <span class="small fw-bold text-dark">{{ $percent }}%</span>
                            </div>
                        </td>
                        <td class="text-end pe-4 fw-bold text-primary fs-6">{{ number_format($caTarif, 0, ',', ' ') }} F CFA</td>
                    </tr>
                @endforeach
                <!-- LIGNE DES TOTAUX GLOBAUX -->
                <tr class="table-dark fw-bold">
                    <td class="ps-4">TOTAL GLOBAL</td>
                    <td>--</td>
                    <td class="text-center">{{ $totalInitiales }}</td>
                    <td class="text-center text-warning">{{ $totalVendues }}</td>
                    <td class="text-center text-info">{{ $totalInitiales - $totalVendues }}</td>
                    <td>
                        @php $totalPercent = $totalInitiales > 0 ? round(($totalVendues / $totalInitiales) * 100) : 0; @endphp
                        <span class="small">{{ $totalPercent }}% de la salle</span>
                    </td>
                    <td class="text-end pe-4 text-warning fs-5">{{ number_format($totalCA, 0, ',', ' ') }} F CFA</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- HISTORIQUE DES DERNIERS BILLETS ÉMIS -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-secondary text-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-receipt-cutoff me-2"></i>Audit d'Émission des Billets (Dernières ventes)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="small text-uppercase">
                <tr>
                    <th class="ps-4">Place</th>
                    <th>Bénéficiaire</th>
                    <th>Catégorie</th>
                    <th>Montant Perçu</th>
                    <th>Mode de règlement</th>
                    <th>Date d'émission</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr>
                        <td class="ps-4 fw-bold text-primary"><span class="badge bg-light text-primary border border-primary">{{ $booking->seat_number }}</span></td>
                        <td class="fw-bold">
                            {{ $booking->customer_name ?? ($booking->user->name ?? 'Client Passager') }}
                        </td>
                        <td class="text-uppercase small fw-semibold">{{ $booking->ticket_type }}</td>
                        <td class="fw-bold text-success">{{ number_format($booking->amount_paid, 0, ',', ' ') }} F</td>
                        <td>
                            @if($booking->status === 'charged_to_room')
                                <span class="badge bg-danger rounded-pill"><i class="bi bi-door-closed me-1"></i> Note Chambre</span>
                            @else
                                <span class="badge bg-success rounded-pill"><i class="bi bi-cash me-1"></i> Comptoir (Direct)</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y à H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted italic">Aucun billet émis pour le moment pour ce concert.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
.bg-light-success { background-color: #f0fdf4 !important; }
</style>
@endsection
