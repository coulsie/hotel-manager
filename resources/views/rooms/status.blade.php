@extends('layouts.app')

@section('title', 'État des Chambres - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-door-closed me-2"></i>État des Chambres en Temps Réel</h1>
</div>

<!-- Rappel de la légende des couleurs -->
<div class="d-flex gap-3 mb-4 flex-wrap p-3 bg-light rounded border">
    <span class="fw-bold text-secondary">Légende :</span>
    <div><span class="badge bg-success me-1">&nbsp;</span> Disponible</div>
    <div><span class="badge bg-danger me-1">&nbsp;</span> Occupée</div>
    <div><span class="badge bg-warning text-dark me-1">&nbsp;</span> En nettoyage</div>
</div>

<!-- Grille interactive des chambres -->
<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-3">
    @foreach($rooms as $room)
        @php
            // Sélection dynamique de la couleur de la carte selon le statut
            $cardBg = 'bg-success text-white';
            if ($room->status === 'occupied') $cardBg = 'bg-danger text-white';
            if ($room->status === 'cleaning') $cardBg = 'bg-warning text-dark';

            // Récupération de la première carte active trouvée
            $activeCard = $room->magneticCards->first();
        @endphp

        <div class="col">
            <div class="card h-100 shadow-sm border-0 {{ $cardBg }}">
                <div class="card-body text-center py-4">
                    <i class="bi {{ $room->status === 'occupied' ? 'bi-door-closed-fill' : 'bi-door-open-fill' }} mb-2" style="font-size: 2.5rem;"></i>
                    <h5 class="card-title fw-bold mb-1">{{ $room->number }}</h5>
                    <p class="card-text small mb-2 text-uppercase fw-medium" style="opacity: 0.8;">{{ $room->type }}</p>

                    <div class="line my-2" style="border-top: 1px dashed rgba(255,255,255,0.4);"></div>

                    @if($room->status === 'occupied' && $activeCard)
                        <div class="small">
                            <span class="d-block mb-1"><i class="bi bi-upc-scan me-1"></i><code>{{ $activeCard->card_uid }}</code></span>
                            <span class="d-block opacity-75" style="font-size: 11px;">
                                <i class="bi bi-clock me-1"></i>Exp: {{ \Carbon\Carbon::parse($activeCard->expires_at)->format('d/m H:i') }}
                            </span>
                        </div>
                    @elseif($room->status === 'available')
                        <span class="badge bg-white text-success fw-bold">Libre</span>
                    @else
                        <span class="badge bg-dark text-white fw-bold">Entretien</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
