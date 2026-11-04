@extends('layouts.app')

@section('title', 'Modifier le Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-pencil-square me-2 text-warning"></i>Modifier le Spectacle : {{ $event->title }}</h1>
</div>

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <span class="fw-bold">Veuillez corriger les erreurs suivantes :</span>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.events.update', $event->id) }}" method="POST">
    @csrf
    @method('PUT') <!-- Indispensable pour la mise à jour Laravel -->

    <div class="row">
        <!-- COLONNE GAUCHE : Infos principales (Table 'events') -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-info"></i>Détails du Spectacle</h5>
                </div>
                <div class="card-body p-4">

                    <!-- Sélection de la Salle de concert (Table: shows) -->
                    <div class="mb-3">
                        <label for="show_id" class="form-label fw-bold text-dark small text-uppercase">Salle de Concert (Show)</label>
                        <select class="form-select @error('show_id') is-invalid @enderror fw-bold" id="show_id" name="show_id" required>
                            @foreach($shows as $show)
                                <option value="{{ $show->id }}" {{ (old('show_id', $event->show_id) == $show->id) ? 'selected' : '' }}>
                                    {{ $show->title }} (Capacité Max : {{ number_format($show->total_capacity, 0, ',', ' ') }} places)
                                </option>
                            @endforeach
                        </select>
                        @error('show_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Titre du concert -->
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold text-dark small text-uppercase">Titre de l'événement</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Date de la représentation -->
                    <div class="mb-3">
                        <label for="performance_date" class="form-label fw-bold text-dark small text-uppercase">Date et Heure du Spectacle</label>
                        <input type="datetime-local" class="form-control @error('performance_date') is-invalid @enderror" id="performance_date" name="performance_date" value="{{ old('performance_date', \Carbon\Carbon::parse($event->performance_date)->format('Y-m-d\TH:i')) }}" required>
                        @error('performance_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-dark small text-uppercase">Description / Notes (Optionnel)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : Grille de Tarification (Table: event_tickets) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-secondary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold"><i class="bi bi-tags me-2 text-warning"></i>Tarification & Grille de Tickets</h5>
                    <button type="button" id="add-ticket-btn" class="btn btn-light btn-sm fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i>Ajouter un Tarif
                    </button>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">Modifiez les tarifs ou capacités. Attention : modifier la capacité réinitialisera le nombre de places disponibles à cette nouvelle valeur.</p>

                    <!-- Conteneur des lignes de tickets -->
                    <div id="tickets-container">

                        <!-- Boucle sur les tickets existants de l'événement -->
                        @forelse($event->tickets as $index => $ticket)
                            <div class="ticket-row p-3 bg-light border rounded-3 mb-3 position-relative shadow-sm">
                                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-ticket-btn" aria-label="Supprimer" title="Supprimer ce tarif"></button>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Type / Catégorie</label>
                                        <select class="form-select fw-bold" name="tickets[{{ $index }}][ticket_type]" required>
                                            <option value="populaire" {{ $ticket->ticket_type == 'populaire' ? 'selected' : '' }}>🎟️ Populaire (Normal)</option>
                                            <option value="honneurs" {{ $ticket->ticket_type == 'honneurs' ? 'selected' : '' }}>🌟 Invité d'Honneur</option>
                                            <option value="vip" {{ $ticket->ticket_type == 'vip' ? 'selected' : '' }}>💎 VIP / Privilège</option>
                                            <option value="gratuit" {{ $ticket->ticket_type == 'gratuit' ? 'selected' : '' }}>🎁 Invitation / Gratuit (0 F)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Capacité</label>
                                        <input type="number" class="form-control text-center fw-bold" name="tickets[{{ $index }}][total_capacity]" value="{{ $ticket->total_capacity }}" min="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Prix (F CFA)</label>
                                        <input type="number" class="form-control text-end fw-bold text-success" name="tickets[{{ $index }}][price]" value="{{ intval($ticket->price) }}" min="0" required>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <!-- Ligne de secours si aucun ticket n'existait (Index 0) -->
                            <div class="ticket-row p-3 bg-light border rounded-3 mb-3 position-relative shadow-sm">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Type / Catégorie</label>
                                        <select class="form-select fw-bold" name="tickets[0][ticket_type]" required>
                                            <option value="populaire">🎟️ Populaire (Normal)</option>
                                            <option value="honneurs">🌟 Invité d'Honneur</option>
                                            <option value="vip">💎 VIP / Privilège</option>
                                            <option value="gratuit">🎁 Invitation / Gratuit (0 F)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Capacité</label>
                                        <input type="number" class="form-control text-center fw-bold" name="tickets[0][total_capacity]" min="1" placeholder="Places" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-muted small text-uppercase">Prix (F CFA)</label>
                                        <input type="number" class="form-control text-end fw-bold text-success" name="tickets[0][price]" min="0" placeholder="Coût" required>
                                    </div>
                                </div>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- BOUTONS D'ACTION VALIDEURS -->
           <!-- BOUTONS D'ACTION VALIDEURS -->
        <div class="col-12 d-flex justify-content-end gap-3">
            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary px-4 rounded-pill fw-bold">Annuler</a>
            <button type="submit" class="btn btn-warning px-5 rounded-pill fw-bold text-dark shadow-sm">
                <i class="bi bi-check-lg me-2"></i>Mettre à jour le Spectacle
            </button>
        </div>
    </div>
</form>

<!-- SCRIPT DE GRILLE TARIFAIRE EN MODE ÉDITION -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.getElementById('tickets-container');
    var addButton = document.getElementById('add-ticket-btn');

    // On calcule l'index de départ dynamiquement par rapport au nombre de tickets pré-existants
    var rowIndex = {{ count($event->tickets) }};

    if (addButton && container) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault(); // Bloque le rechargement parasite de l'écran

            var newRow = document.createElement('div');
            newRow.className = 'ticket-row p-3 bg-light border rounded-3 mb-3 position-relative shadow-sm';

            // Concaténation JavaScript classique 100% compatible avec le compilateur Blade Laravel
            var html = "";
            html += "<button type=\"button\" class=\"btn-close position-absolute top-0 end-0 m-2 remove-ticket-btn\" aria-label=\"Supprimer\" title=\"Supprimer ce tarif\"></button>";
            html += "<div class=\"row g-2\">";
            html += "    <div class=\"col-md-5\">";
            html += "        <label class=\"form-label fw-bold text-muted small text-uppercase\">Type / Catégorie</label>";
            html += "        <select class=\"form-select fw-bold\" name=\"tickets[" + rowIndex + "][ticket_type]\" required>";
            html += "            <option value=\"populaire\">🎟️ Populaire (Normal)</option>";
            html += "            <option value=\"honneurs\">🌟 Invité d'Honneur</option>";
            html += "            <option value=\"vip\" selected>💎 VIP / Privilège</option>";
            html += "            <option value=\"gratuit\">🎁 Invitation / Gratuit (0 F)</option>";
            html += "        </select>";
            html += "    </div>";
            html += "    <div class=\"col-md-3\">";
            html += "        <label class=\"form-label fw-bold text-muted small text-uppercase\">Capacité</label>";
            html += "        <input type=\"number\" class=\"form-control text-center fw-bold\" name=\"tickets[" + rowIndex + "][total_capacity]\" min=\"1\" placeholder=\"Places\" required>";
            html += "    </div>";
            html += "    <div class=\"col-md-4\">";
            html += "        <label class=\"form-label fw-bold text-muted small text-uppercase\">Prix (F CFA)</label>";
            html += "        <input type=\"number\" class=\"form-control text-end fw-bold text-success\" name=\"tickets[" + rowIndex + "][price]\" min=\"0\" placeholder=\"Coût\" required>";
            html += "    </div>";
            html += "</div>";

            newRow.innerHTML = html;
            container.appendChild(newRow);

            rowIndex++; // Incrémente l'index unique pour le prochain ticket
        });

        // Gestion de la suppression d'une ligne (ancienne ou nouvelle)
        container.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-ticket-btn')) {
                var rowToRemove = e.target.closest('.ticket-row');
                if (rowToRemove) {
                    rowToRemove.remove();
                }
            }
        });
    }
});
</script>
@endsection
