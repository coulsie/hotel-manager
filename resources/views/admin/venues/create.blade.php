@extends('layouts.app')

@section('title', 'Créer une Nouvelle Salle')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('admin.venues.index') }}" class="text-decoration-none">Salles</a></li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Nouvelle Salle</li>
        </ol>
    </nav>
    <h2 class="fw-bolder text-dark">🏛️ Ajouter une Salle de Spectacle</h2>
</div>

<div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 800px;">
    <div class="card-header py-3 bg-dark text-white">
        <h6 class="m-0 fw-bold"><i class="bi bi-building-add me-2 text-info"></i>Fiche technique de l'infrastructure</h6>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.venues.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- Nom de la salle -->
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark text-uppercase small">Nom de la Salle / Espace</label>
                    <input type="text" name="title" class="form-control bg-light border-0 fw-bold"
                           placeholder="ex: Grand Théâtre Alpha, Salle Koala..." required value="{{ old('title') }}">
                    @error('title') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Capacité d'accueil -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark text-uppercase small">Capacité Maximale (Places)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-people-fill"></i></span>
                        <input type="number" name="total_capacity" class="form-control bg-light border-0 fw-bold text-end"
                               min="1" placeholder="350" required value="{{ old('total_capacity') }}">
                    </div>
                    @error('total_capacity') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Histoire et Emplacement -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-uppercase small">Histoire & Emplacement (Description)</label>
                    <textarea name="description" class="form-control bg-light border-0" rows="5"
                              placeholder="Renseignez ici l'emplacement dans l'hôtel, l'histoire de la salle ou les contraintes techniques de la scène...">{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Soumission -->
            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('admin.venues.index') }}" class="btn btn-light rounded-pill px-4 me-2 fw-medium">Annuler</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Enregistrer l'espace
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
