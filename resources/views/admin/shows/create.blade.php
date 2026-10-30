@extends('layouts.app')

@section('title', 'Programmer un Nouveau Spectacle')

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
                <span class="text-muted fw-normal me-3">/</span> Programmer un Spectacle
            </li>
        </ol>
    </nav>
</div>

<div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 850px;">
    <div class="card-header py-3 bg-dark text-white">
        <h6 class="m-0 fw-bold"><i class="bi bi-calendar-plus me-2 text-info"></i>Création d'une représentation culturelle</h6>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.shows.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- Titre du Spectacle -->
                <div class="col-md-8">
                    <label class="form-label fw-bold text-dark text-uppercase small">Titre du Spectacle</label>
                    <input type="text" name="title" class="form-control bg-light border-0 fw-bold"
                           placeholder="ex: Concert Acoustique de Jazz, Pièce d'humour..." required value="{{ old('title') }}">
                    @error('title') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Prix du Ticket (Optionnel !) -->
                <div class="col-md-4">
                    <label class="form-label fw-bold text-dark text-uppercase small">Prix du Ticket (Optionnel)</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden">
                        <span class="input-group-text bg-light border-0 fw-bold">F CFA</span>
                        <input type="number" name="ticket_price" class="form-control bg-light border-0 fw-bold text-end"
                               min="0" step="500" placeholder="0 (Gratuit)" value="{{ old('ticket_price') }}">
                    </div>
                    @error('ticket_price') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Choix de la Salle de Spectacle (Dynamique depuis la BDD) -->
                <div class="col-md-6">
                    <div class="p-3 rounded-4" style="background-color: #f0f9ff; border-left: 5px solid #0284c7;">
                        <label class="form-label fw-bold text-info"><i class="bi bi-building-fill me-1"></i> Affecter à une Salle</label>
                        <select name="show_id" class="form-select border-0 bg-white fw-bold shadow-sm" required>
                            <option value="" disabled selected>-- Choisir l'espace d'accueil --</option>
                            @foreach($venues as $venue)
                                <option value="{{ $venue->id }}" {{ old('show_id') == $venue->id ? 'selected' : '' }}>
                                    {{ strtoupper($venue->title) }} (Max: {{ $venue->total_capacity }} places)
                                </option>
                            @endforeach
                        </select>
                        @error('show_id') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Date et Heure de la performance -->
                <div class="col-md-6">
                    <div class="p-3 rounded-4" style="background-color: #fff1f2; border-left: 5px solid #f43f5e;">
                        <label class="form-label fw-bold text-danger"><i class="bi bi-clock-history me-1"></i> Date & Heure du Spectacle</label>
                        <input type="datetime-local" name="performance_date" class="form-control border-0 bg-white fw-bold shadow-sm text-danger"
                               required value="{{ old('performance_date') }}">
                        @error('performance_date') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Description / Résumé de l'événement -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark text-uppercase small">Synopsis / Description du Spectacle</label>
                    <textarea name="description" class="form-control bg-light border-0" rows="4"
                              placeholder="Ajoutez des détails sur les artistes, le programme ou le déroulement de la soirée...">{{ old('description') }}</textarea>
                    @error('description') <span class="text-danger small fw-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Boutons de validation -->
            <div class="text-end mt-4 pt-3 border-top">
                <a href="{{ route('admin.venues.index') }}" class="btn btn-light rounded-pill px-4 me-2 fw-medium">Annuler</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-calendar-check me-1"></i> Planifier la Représentation
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
