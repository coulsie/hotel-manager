@extends('layouts.app')

@section('title', 'Gestion des Salles de Spectacle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bolder text-dark mb-0">🏛️ Salles de Spectacle</h2>
        <p class="text-muted small mb-0">Configuration et historique des espaces physiques de l'établissement</p>
    </div>

    {{-- Zone des boutons d'actions réunis --}}
    <div class="d-flex gap-2">
        <!-- NOUVEAU BOUTON : Créer une salle de spectacle physique -->
        <a href="{{ route('admin.venues.create') }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Ajouter une Salle
        </a>

        <!-- BOUTON EXISTANT : Planifier un événement/spectacle -->
        <a href="{{ route('admin.shows.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-calendar-plus me-1"></i> Programmer un Spectacle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom">
        <h6 class="m-0 fw-bold text-dark"><i class="bi bi-building me-2 text-primary"></i>Liste des Espaces & Salles de Spectacle</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-uppercase small fw-bold text-muted">
                    <th class="ps-4">Nom de la Salle / Description</th>
                    <th class="text-center">Capacité Maximale</th>
                    <th class="text-center">Spectacles Planifiés</th>
                    <th class="pe-4">Statut Opérationnel</th>
                </tr>
            </thead>
            <tbody>
                @forelse($venues as $venue)
                    <tr>
                        <td class="ps-4 py-3">
                            <strong class="text-dark d-block text-uppercase">{{ $venue->title }}</strong>
                            <small class="text-muted">{{ $venue->description ?? 'Aucun historique ou emplacement configuré.' }}</small>
                        </td>
                        <td class="text-center fw-bold text-primary">
                            <i class="bi bi-people-fill me-1"></i>{{ $venue->total_capacity }} places
                        </td>
                        <td class="text-center">
                            <span class="badge bg-soft-indigo text-indigo rounded-pill px-3 py-2 fw-bold" style="background-color: #f0f3ff; color: #4f46e5;">
                                <i class="bi bi-calendar-event me-1"></i> {{ $venue->events_count }} Représentation(s)
                            </span>
                        </td>
                        <td class="pe-4">
                            <span class="badge bg-success rounded-pill px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i>Actif / Configuré</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-building-exclamation fs-1 d-block mb-2 text-secondary"></i>
                            Aucune salle de spectacle configurée pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
