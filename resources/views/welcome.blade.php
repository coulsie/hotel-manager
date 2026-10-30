@extends('layouts.app')

@section('title', 'Tableau de bord - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-speedometer2 me-2"></i>Tableau de bord général</h1>
</div>

<!-- Grille de statistiques rapides (Cards Bootstrap dynamiques) -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">Chambres Occitanes</h5>
                    <h2 class="fw-bold mb-0">{{ $roomsStats }}</h2>
                </div>
                <i class="bi bi-door-open" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">Chiffre Restaurant</h5>
                    <h2 class="fw-bold mb-0">{{ $revenueStats }}</h2>
                </div>
                <i class="bi bi-cart-check" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">Accès IoT (24h)</h5>
                    <h2 class="fw-bold mb-0">{{ $iotStats }}</h2>
                </div>
                <i class="bi bi-wifi" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title">Spectacle ce soir</h5>
                    <h2 class="fw-bold mb-0">{{ $showStats }}</h2>
                </div>
                <i class="bi bi-theater" style="font-size: 2.5rem; opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Section Suivi Temps Réel des cartes RFID -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0 text-dark"><i class="bi bi-broadcast me-2 text-danger"></i>Suivi des accès à distance (IoT en direct)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Heure du scan</th>
                        <th>N° Chambre</th>
                        <th>Code RFID Carte</th>
                        <th>Action détectée</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->scanned_at)->format('H:i:s') }}</td>
                            <td><strong>{{ $log->room->number ?? 'N/A' }}</strong></td>
                            <td><code>{{ $log->magneticCard->card_uid ?? 'Inconnue' }}</code></td>
                            <td>
                                @if($log->action == 'access_granted')
                                    Ouverture de porte
                                @else
                                    Tentative d'accès erronée
                                @endif
                            </td>
                            <td>
                                @if($log->action == 'access_granted')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Autorisé</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Refusé</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <!-- Lignes de démonstration figées si aucun scan n'existe en base de données -->
                        <tr>
                            <td>10:32:15</td>
                            <td><strong>Chambre 104</strong></td>
                            <td><code>RFID_98X76A</code></td>
                            <td>Ouverture de porte</td>
                            <td><span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Autorisé</span></td>
                        </tr>
                        <tr>
                            <td>10:28:40</td>
                            <td><strong>Chambre 212</strong></td>
                            <td><code>RFID_44B12C</code></td>
                            <td>Tentative d'accès erronée</td>
                            <td><span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Refusé</span></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
