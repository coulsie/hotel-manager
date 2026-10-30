@extends('layouts.app')

@section('title', 'Ajouter une Salle de Spectacle')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1 d-flex align-items-center gap-2">
            <li class="breadcrumb-item d-flex align-items-center m-0">
                <a href="{{ route('admin.venues.index') }}" class="btn-poussoir-rouge" style="background-color: #6366f1; box-shadow: 0 4px 0 #4f46e5;">
                    <i class="bi bi-building me-1"></i> Salles
                </a>
            </li>
            <li class="breadcrumb-item active text-primary fw-bold fs-6 d-flex align-items-center ms-1" aria-current="page">
                <span class="text-muted fw-normal me-3">/&nbsp;</span> Configuration de l'Espace
            </li>
        </ol>
    </nav>
</div>

<div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 850px;">
    <div class="card-header py-3 bg-dark text-white">
        <h6 class="m-0 fw-bold"><i class="bi bi-building-add me-2 text-info"></i>Fiche technique de la Salle de Spectacle</h6>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.venues.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- Nom de la salle -->
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark text-uppercase small">Nom de la Salle / Espace</label>
                    <input type="text" name="title" class="form-control bg-light border-0 fw-bold"
                           placeholder="ex: Grand Théâtre Alpha, Salle des Fêtes..." required value="{{ old('title') }}">
                    @error('title') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Capacité d'accueil -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark text-uppercase small">Capacité Maximale (Places)</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-people-fill text-secondary"></i></span>
                        <input type="number" name="total_capacity" class="form-control bg-light border-0 fw-bold text-end"
                               min="1" placeholder="250" required value="{{ old('total_capacity') }}">
                    </div>
                    @error('total_capacity') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Histoire / Emplacement technique -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-uppercase small">Histoire, Emplacement & Contraintes (Description)</label>
                    <textarea name="description" class="form-control bg-light border-0" rows="5"
                              placeholder="Renseignez ici l'emplacement précis dans l'hôtel, l'histoire de la salle, ou des spécificités sur le matériel disponible..."></textarea>
                    @error('description') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('admin.venues.index') }}" class="btn btn-light rounded-pill px-4 me-2 fw-medium">Annuler</a>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-1"></i> Créer la Salle
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Style bouton poussoir pour le fil d'Ariane */
.btn-poussoir-rouge {
    display: inline-block;
    padding: 6px 16px;
    font-size: 0.85rem;
    font-weight: bold;
    color: #ffffff !important;
    text-decoration: none !important;
    border-radius: 8px;
    transition: all 0.1s ease;
}
.btn-poussoir-rouge:active {
    box-shadow: 0 1px 0 #4f46e5 !important;
    transform: translateY(3px);
}
.breadcrumb-item + .breadcrumb-item::before {
    display: none !important;
}
</style>
@endsection
