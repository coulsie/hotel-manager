@extends('layouts.app')

@section('title', 'Liste des Spectacles Programmés - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-calendar-event-fill text-danger me-2"></i>Gestion des <span style="color: #7c3aed;">Spectacles Programmés</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Planification des concerts, grilles tarifaires multi-catégories et suivi des jauges</p>
    </div>
    <a href="{{ route('admin.events.create') }}" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow" style="background: linear-gradient(135deg, #e11d48 0%, #9333ea 100%); border: none;">
        <i class="bi bi-calendar-plus-fill text-warning me-2"></i>Programmer un Spectacle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $totalEventsCount = $events->count();
    $totalSeatsOffered = $events->sum(function($ev) {
        return $ev->tickets->sum('total_capacity');
    });
    $totalSeatsAvailable = $events->sum(function($ev) {
        return $ev->tickets->sum('available_seats');
    });
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DES ÉVÉNEMENTS (DÉGRADÉS ÉCLATANTS)             -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Concerts programmés -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #6d28d9 0%, #db2777 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Concerts Programmés</span>
                    <h2 class="fw-bold mb-0">{{ $totalEventsCount }} <small class="fs-6 fw-normal">événement(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-music-note-list fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Capacité totale mise en vente -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Billets Mis en Vente</span>
                    <h2 class="fw-bold mb-0">{{ number_format($totalSeatsOffered, 0, ',', ' ') }} <small class="fs-6 fw-normal">places</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-ticket-perforated-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Places restantes disponibles -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Places Restantes</span>
                    <h2 class="fw-bold mb-0">{{ number_format($totalSeatsAvailable, 0, ',', ' ') }} <small class="fs-6 fw-normal">libres</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-people-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. TABLEAU COLORÉ DES SPECTACLES ET GRILLES TARIFAIRES                -->
<!-- ===================================================================== -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%);">
        <span><i class="bi bi-stars text-warning me-2 fs-5"></i>Catalogue des Spectacles & Tarifs</span>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ $totalEventsCount }} événement(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="small text-uppercase" style="background-color: #f3e8ff; color: #581c87;">
                <tr>
                    <th class="ps-4 py-3" style="width: 6%;">#ID</th>
                    <th class="py-3" style="width: 24%;">Spectacle / Concert</th>
                    <th class="py-3" style="width: 18%;">Salle (Show)</th>
                    <th class="py-3" style="width: 15%;">Date & Heure</th>
                    <th class="py-3" style="width: 25%;">Grille Tarifaire (Places restantes)</th>
                    <th class="text-end pe-4 py-3" style="width: 12%;">Actions</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($events as $event)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary-subtle text-dark fw-bold rounded-pill px-2 py-1">#{{ $event->id }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $event->title }}</div>
                            @if($event->description)
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 240px;" title="{{ $event->description }}">
                                    {{ $event->description }}
                                </small>
                            @else
                                <small class="text-muted fst-italic">Aucune description</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold px-3 py-2 rounded-pill">
                                <i class="bi bi-building-fill me-1"></i>
                                {{ $event->venue->title ?? 'Salle inconnue (ID: '.$event->show_id.')' }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark small">
                                <i class="bi bi-calendar3 me-1 text-danger"></i>
                                {{ \Carbon\Carbon::parse($event->performance_date)->format('d/m/Y') }}
                            </div>
                            <div class="mt-1">
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2">
                                    <i class="bi bi-clock-fill me-1"></i>{{ \Carbon\Carbon::parse($event->performance_date)->format('H\hi') }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @forelse($event->tickets as $ticket)
                                    @php
                                        $typeBadge = 'bg-info-subtle text-info-emphasis border-info-subtle';
                                        if(strtolower($ticket->ticket_type) === 'vip') {
                                            $typeBadge = 'bg-warning text-dark border-warning';
                                        }
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center bg-white border rounded-3 px-2 py-1 shadow-sm small">
                                        <span class="badge {{ $typeBadge }} border text-uppercase fw-bold" style="font-size: 0.7rem;">
                                            {{ $ticket->ticket_type }}
                                        </span>
                                        <span class="fw-bold text-success mx-2">
                                            {{ number_format($ticket->price, 0, ',', ' ') }} F
                                        </span>
                                        <span class="badge {{ $ticket->available_seats > 0 ? 'bg-primary' : 'bg-danger' }} rounded-pill px-2">
                                            {{ $ticket->available_seats }}/{{ $ticket->total_capacity }}
                                        </span>
                                    </div>
                                @empty
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                                        <i class="bi bi-exclamation-circle-fill me-1"></i> Aucun tarif configuré
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm" title="Modifier le spectacle">
                                    <i class="bi bi-pencil-square me-1"></i> Modifier
                                </a>
                                <a href="{{ route('admin.events.sales', $event->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" title="Voir l'état des ventes">
                                    <i class="bi bi-graph-up-arrow me-1"></i> Ventes
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 bg-light">
                            <div class="text-primary mb-2"><i class="bi bi-calendar-x fs-1"></i></div>
                            <h5 class="text-dark fw-bold">Aucun spectacle n'est programmé actuellement</h5>
                            <p class="text-muted small mb-0">Cliquez sur le bouton « Programmer un Spectacle » en haut à droite pour ajouter votre premier événement.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
