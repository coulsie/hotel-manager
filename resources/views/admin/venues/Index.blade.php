@extends('layouts.app')

@section('title', 'Gestion des Salles de Spectacle')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bolder text-dark mb-0">🏛️ Salles de Spectacle</h2>
        <p class="text-muted small mb-0">Configuration et historique des espaces physiques de l'établissement</p>
    </div>
    <a href="{{ route('admin.venues.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Ajouter une Salle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="row g-4">
    @forelse($venues as $venue)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold text-dark mb-0 text-uppercase">{{ $venue->title }}</h5>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-bold">
                            <i class="bi bi-people-fill me-1"></i> {{ $venue->total_capacity }} pl.
                        </span>
                    </div>

                    <p class="text-muted small flex-grow-1 mb-4" style="line-height: 1.5;">
                        <i class="bi bi-journal-text me-1 text-secondary"></i>
                        {{ Str::limit($venue->description, 140, '...') }}
                    </p>

                    <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                        <span class="small text-indigo fw-medium">
                            <i class="bi bi-calendar-event me-1"></i> {{ $venue->events_count }} Représentations
                        </span>
                        <span class="badge bg-light text-muted border">ID: #{{ $venue->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-building-exclamation fs-1 d-block mb-2 text-secondary"></i>
                Aucune salle de spectacle configurée pour le moment.
            </div>
        </div>
    @endif
</div>
@endsection
