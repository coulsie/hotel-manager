@extends('layouts.app')

@section('title', 'Rack Hôtelier - Planning Graphique')

@section('content')
<style>
    .rack-table th, .rack-table td {
        min-width: 44px;
        height: 46px;
        vertical-align: middle;
        padding: 2px;
        border-color: #e2e8f0;
    }
    .sticky-room-col {
        position: sticky;
        left: 0;
        z-index: 5;
        background-color: #ffffff;
        min-width: 170px !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }
    .rack-cell-free {
        background-color: #ffffff;
        transition: background-color 0.15s;
        cursor: pointer;
    }
    .rack-cell-free:hover {
        background-color: #eff6ff;
    }
    .rack-bar-checked_in {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
    }
    .rack-bar-confirmed {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
    }
    .rack-bar-maintenance {
        background-color: #64748b;
        color: #fff;
    }
    .today-col {
        background-color: #fef9c3 !important;
        border-left: 2px solid #eab308 !important;
        border-right: 2px solid #eab308 !important;
    }
</style>

<div class="container-fluid py-3">
    <!-- En-tête & Navigation Mensuelle -->
    <div class="d-flex flex-wrap justify-content-between align-items-center pb-3 mb-3 border-bottom gap-2">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Rack Hôtelier (Planning Graphique)
            </h3>
            <p class="text-muted small mb-0">Vue d'ensemble des occupations en cours et des réservations confirmées.</p>
        </div>

        <!-- Sélecteur de mois -->
        <div class="d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm border">
            <a href="{{ route('reservations.rack', ['month' => $prevMonth]) }}" class="btn btn-sm btn-light rounded-circle">
                <i class="bi bi-chevron-left"></i>
            </a>
            <span class="fw-bold px-3 text-uppercase text-primary">
                {{ $startOfMonth->translatedFormat('F Y') }}
            </span>
            <a href="{{ route('reservations.rack', ['month' => $nextMonth]) }}" class="btn btn-sm btn-light rounded-circle">
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="{{ route('reservations.rack') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 ms-1">
                Aujourd'hui
            </a>
        </div>

        <!-- Bouton retour vers la liste -->
        <a href="{{ route('reservations.index') }}" class="btn btn-dark rounded-pill px-4 fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1 text-warning"></i> Nouvelle Réservation
        </a>
    </div>

    <!-- Légende -->
    <div class="d-flex flex-wrap gap-3 mb-3 small">
        <span class="d-flex align-items-center gap-2">
            <span class="rounded-2 d-inline-block" style="width: 18px; height: 18px; background: #10b981;"></span>
            <strong>Séjour en cours (Check-in)</strong>
        </span>
        <span class="d-flex align-items-center gap-2">
            <span class="rounded-2 d-inline-block" style="width: 18px; height: 18px; background: #f97316;"></span>
            <strong>Réservation confirmée (Arrhes/Attente)</strong>
        </span>
        <span class="d-flex align-items-center gap-2">
            <span class="rounded-2 border d-inline-block bg-white" style="width: 18px; height: 18px;"></span>
            <strong>Chambre libre (Cliquer pour réserver)</strong>
        </span>
    </div>

    <!-- Grille du Rack -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-bordered rack-table mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="sticky-room-col text-start ps-3 text-uppercase small text-muted">Chambre</th>
                        @foreach($days as $day)
                            @php
                                $isToday = $day->isToday();
                                $isWeekend = $day->isWeekend();
                            @endphp
                            <th class="small {{ $isToday ? 'bg-warning text-dark fw-bold' : ($isWeekend ? 'bg-secondary-subtle' : '') }}">
                                <div style="font-size: 0.65rem;" class="text-uppercase">{{ substr($day->translatedFormat('D'), 0, 3) }}</div>
                                <div>{{ $day->format('d') }}</div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                                <tbody>
                    @foreach($rackMatrix as $item)
                        @php $room = $item['room']; @endphp
                        <tr>
                            <!-- Colonne fixe Chambre -->
                            <td class="sticky-room-col text-start ps-3">
                                <div class="fw-bold text-dark">Ch. {{ $room->number }}</div>
                                <div class="text-muted" style="font-size: 0.72rem;">
                                    {{ $room->type }} • {{ number_format($room->price_per_night, 0, ',', ' ') }} F
                                </div>
                            </td>

                            <!-- Cellules des jours -->
                            @foreach($days as $day)
                                @php
                                    $dateStr = $day->toDateString();
                                    $cell = $item['cells'][$dateStr];
                                    $isToday = $day->isToday();
                                @endphp

                                @if($cell['state'] === 'checked_in' || $cell['state'] === 'confirmed')
                                    @php
                                        $res = $cell['reservation'] ?? null;
                                        $tooltip = $res
                                            ? $cell['client'] . ' (Du ' . $res->check_in_date->format('d/m') . ' au ' . $res->check_out_date->format('d/m') . ')'
                                            : $cell['client'] . ' (Séjour en cours)';
                                        $targetUrl = ($cell['state'] === 'checked_in')
                                            ? route('billing.room', $room->id)
                                            : route('reservations.index');
                                    @endphp
                                    <td class="p-0" style="cursor: pointer;" onclick="window.location.href='{{ $targetUrl }}'" title="{{ $tooltip }} - Cliquer pour ouvrir">
                                        <div class="rack-bar-{{ $cell['state'] }} h-100 d-flex align-items-center justify-content-center px-1 small fw-semibold text-truncate"
                                            style="font-size: 0.7rem; {{ !empty($cell['is_start']) ? 'border-top-left-radius: 8px; border-bottom-left-radius: 8px; margin-left: 3px;' : '' }}">
                                            @if(!empty($cell['is_start']) || $day->day === 1)
                                                {{ \Illuminate\Support\Str::limit($cell['client'], 10) }}
                                            @else
                                                •
                                            @endif
                                        </div>
                                    </td>
                                @elseif($cell['state'] === 'maintenance')
                                    <td class="rack-bar-maintenance small" title="Maintenance">
                                        <i class="bi bi-tools"></i>
                                    </td>
                                @else
                                    <td class="rack-cell-free {{ $isToday ? 'today-col' : '' }}"
                                        onclick="window.location.href='{{ route('reservations.index', ['room_id' => $room->id, 'check_in_date' => $dateStr]) }}'"
                                        title="Libre le {{ $day->format('d/m/Y') }} - Cliquer pour réserver">
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection
