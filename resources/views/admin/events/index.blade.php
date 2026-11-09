@extends('layouts.app')

@section('title', 'Liste des Spectacles Programmés - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-calendar-event me-2 text-danger"></i>Gestion des Spectacles Programmés</h1>
    <a href="{{ route('admin.events.create') }}" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-calendar-plus me-1"></i> Programmer un Spectacle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
       <table class="table table-hover align-middle mb-0">
    <thead class="table-dark text-uppercase small">
        <tr>
            <th class="ps-4 py-3" style="width: 5%;">#ID</th>
            <th class="py-3" style="width: 25%;">Spectacle / Concert</th>
            <th class="py-3" style="width: 20%;">Salle (Show)</th>
            <th class="py-3" style="width: 15%;">Date & Heure</th>
            <th class="py-3" style="width: 25%;">Grille Tarifaire (Places restantes)</th>
            <th class="text-end pe-4 py-3" style="width: 10%;">Actions</th>
        </tr>
    </thead>
    <tbody class="border-top-0">
        @forelse($events as $event)
            <tr>
                <td class="ps-4 fw-bold text-secondary">#{{ $event->id }}</td>
                <td>
                    <div class="fw-bold text-dark fs-6">{{ $event->title }}</div>
                    @if($event->description)
                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="{{ $event->description }}">
                            {{ $event->description }}
                        </small>
                    @else
                        <small class="text-muted italic">Aucune description</small>
                    @endif
                </td>
                <td>
                    <span class="badge bg-light text-dark border fw-semibold px-3 py-2 rounded-pill">
                        <i class="bi bi-building me-1 text-primary"></i>
                        {{ $event->venue->title ?? 'Salle inconnue (ID: '.$event->show_id.')' }}
                    </span>
                </td>
                <td>
                    <div class="fw-bold text-dark small">
                        <i class="bi bi-calendar3 me-1 text-danger"></i>
                        {{ \Carbon\Carbon::parse($event->performance_date)->format('d/m/Y') }}
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="bi bi-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($event->performance_date)->format('H\hi') }}
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column gap-1">
                        @forelse($event->tickets as $ticket)
                            <div class="d-flex justify-content-between align-items-center bg-light border rounded px-2 py-1 small">
                                <span class="fw-semibold text-uppercase text-secondary" style="font-size: 0.75rem;">
                                    {{ $ticket->ticket_type }}
                                </span>
                                <span class="fw-bold text-success mx-2">
                                    {{ number_format($ticket->price, 0, ',', ' ') }} F
                                </span>
                                <span class="badge {{ $ticket->available_seats > 0 ? 'bg-secondary' : 'bg-danger' }} rounded-pill">
                                    {{ $ticket->available_seats }}/{{ $ticket->total_capacity }}
                                </span>
                            </div>
                        @empty
                            <span class="text-danger small italic"><i class="bi bi-exclamation-circle me-1"></i> Aucun tarif configuré</span>
                        @endforelse
                    </div>
                </td>
                <!-- COLONNE ACTIONS CORRIGÉE -->
                <td class="text-end pe-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm" title="Modifier le spectacle">
                            <i class="bi bi-pencil-square me-1"></i> Modifier
                        </a>
                        <!-- Ajoutez ce bouton à côté du bouton de modification existant -->
                        <a href="{{ route('admin.events.sales', $event->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm" title="Voir l'état des ventes">
                            <i class="bi bi-graph-up-arrow me-1"></i> Ventes
                        </a>

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-muted mb-2"><i class="bi bi-calendar-x fs-1"></i></div>
                    <h5 class="text-secondary fw-semibold">Aucun spectacle n'est programmé actuellement</h5>
                    <p class="text-muted small">Cliquez sur le bouton en haut à droite pour ajouter votre premier événement.</p>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

    </div>
</div>
@endsection
