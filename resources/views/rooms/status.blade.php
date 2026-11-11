@extends('layouts.app')

@section('title', 'État des Chambres - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>État des Chambres en Temps Réel
        </h1>
        <p class="text-muted small mb-0 mt-1">Supervision visuelle de l'occupation et des badges RFID actifs</p>
    </div>
    <a href="{{ route('checkin.form') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Nouveau Check-in
    </a>
</div>

@php
    $countAvailable = $rooms->where('status', 'available')->count();
    $countOccupied  = $rooms->where('status', 'occupied')->count();
    $countCleaning  = $rooms->where('status', 'cleaning')->count();
@endphp

<!-- Légende interactive avec compteurs en direct -->
<div class="d-flex gap-3 mb-4 flex-wrap align-items-center p-3 bg-white rounded-4 shadow-sm border">
    <span class="fw-bold text-dark small text-uppercase me-2"><i class="bi bi-palette-fill text-primary me-1"></i> Statuts :</span>

    <div class="d-flex align-items-center bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold small">
        <span class="rounded-circle bg-success me-2" style="width: 10px; height: 10px;"></span>
        Disponible ({{ $countAvailable }})
    </div>

    <div class="d-flex align-items-center bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold small">
        <span class="rounded-circle bg-danger me-2" style="width: 10px; height: 10px;"></span>
        Occupée ({{ $countOccupied }})
    </div>

    <div class="d-flex align-items-center bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1 rounded-pill fw-bold small">
        <span class="rounded-circle bg-warning me-2" style="width: 10px; height: 10px;"></span>
        En nettoyage ({{ $countCleaning }})
    </div>
</div>

<!-- Grille interactive des chambres -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
    @foreach($rooms as $room)
        @php
            // Dégradés modernes selon le statut
            $cardStyle = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;'; // Disponible (Émeraude)
            if ($room->status === 'occupied') {
                $cardStyle = 'background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: #ffffff;'; // Occupée (Rubis)
            } elseif ($room->status === 'cleaning') {
                $cardStyle = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff;'; // Nettoyage (Ambre)
            }

            // Récupération de la première carte active trouvée
            $activeCard = $room->magneticCards->first();
        @endphp

        <div class="col">
            <div class="card h-100 shadow border-0 rounded-4 room-card-hover overflow-hidden" style="{{ $cardStyle }}">
                <div class="card-body text-center p-4 d-flex flex-column justify-content-between">

                    <div>
                        <!-- Icône dans un cercle translucide -->
                        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-white bg-opacity-25" style="width: 60px; height: 60px;">
                            <i class="bi {{ $room->status === 'occupied' ? 'bi-door-closed-fill' : ($room->status === 'cleaning' ? 'bi-brush-fill' : 'bi-door-open-fill') }} fs-2 text-white"></i>
                        </div>

                        <h4 class="card-title fw-black mb-1">{{ $room->number }}</h4>
                        <span class="badge bg-black bg-opacity-25 text-white rounded-pill px-3 py-1 small text-uppercase fw-semibold" style="letter-spacing: 0.5px;">
                            {{ $room->type }}
                        </span>
                    </div>

                    <div>
                        <div class="my-3" style="border-top: 1px dashed rgba(255,255,255,0.4);"></div>

                        @if($room->status === 'occupied' && $activeCard)
                            <div class="small">
                                <span class="d-inline-block bg-white text-danger fw-bold px-2 py-1 rounded-3 mb-1 shadow-sm" style="font-size: 0.75rem;">
                                    <i class="bi bi-upc-scan me-1"></i>{{ $activeCard->card_uid }}
                                </span>
                                <span class="d-block text-white opacity-90 mt-1 fw-semibold" style="font-size: 0.75rem;">
                                    <i class="bi bi-clock-history me-1"></i>Départ : {{ \Carbon\Carbon::parse($activeCard->expires_at)->format('d/m à H:i') }}
                                </span>
                            </div>
                        @elseif($room->status === 'available')
                            <span class="badge bg-white text-success fw-bold px-3 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Libre
                            </span>
                        @else
                            <span class="badge bg-white text-warning-emphasis fw-bold px-3 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-stars me-1"></i> Entretien
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    .room-card-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .room-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.2) !important;
    }
</style>
@endsection
