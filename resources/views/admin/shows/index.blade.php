@extends('layouts.app')

@section('title', 'État des Salles de Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-building me-2 text-primary"></i>État des Salles de Spectacle</h1>
    <!-- Ce bouton ouvre instantanément la fenêtre modale en mode Création -->
    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="openShowModal()">
        <i class="bi bi-plus-circle me-1"></i> Ajouter une Salle
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- Grille d'affichage des salles de la table 'shows' -->
<div class="row">
    @forelse($venues as $venue)
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="card-title fw-bold text-dark mb-0">{{ $venue->title }}</h5>
                        <span class="badge bg-secondary rounded-pill px-3 py-2 fw-semibold">
                            <i class="bi bi-people-fill me-1"></i> {{ number_format($venue->total_capacity, 0, ',', ' ') }} places
                        </span>
                    </div>

                    <p class="card-text text-muted small mb-4">
                        {{ $venue->description ?? 'Aucune description fournie pour cette infrastructure.' }}
                    </p>

                    <div class="border-top pt-3 d-flex justify-content-end gap-2">
                        <!-- Ce bouton transmet les informations SQL de la salle à la modale en JavaScript -->
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"
                                onclick="openShowModal({{ json_encode($venue) }})">
                            <i class="bi bi-pencil me-1"></i> Modifier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted mb-3"><i class="bi bi-building fs-1"></i></div>
            <p class="text-secondary fw-semibold">Aucune salle de spectacle configurée pour le moment.</p>
        </div>
    @endforelse
</div>

<!-- FENÊTRE MODALE UNIQUE POUR LA CRÉATION ET LA MODIFICATION -->
<div class="modal fade" id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="showModalLabel">Gestion de la Salle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="showForm" method="POST" action="">
                @csrf
                <!-- Conteneur pour la méthode PUT en mode modification -->
                <div id="method-container"></div>

                <div class="modal-body p-4">
                    <!-- Nom de la Salle -->
                    <div class="mb-3">
                        <label for="modal_title" class="form-label fw-bold text-dark small text-uppercase">Nom / Titre de la Salle</label>
                        <input type="text" class="form-control fw-bold" id="modal_title" name="title" placeholder="Ex: Salle de Concert Opéra" required>
                    </div>

                    <!-- Capacité Maximale -->
                    <div class="mb-3">
                        <label for="modal_capacity" class="form-label fw-bold text-dark small text-uppercase">Capacité Totale (Places)</label>
                        <input type="number" class="form-control fw-bold text-primary" id="modal_capacity" name="total_capacity" min="1" placeholder="Ex: 500" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-0">
                        <label for="modal_description" class="form-label fw-bold text-dark small text-uppercase">Description (Optionnel)</label>
                        <textarea class="form-control" id="modal_description" name="description" rows="3" placeholder="Équipements, agencement..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0 p-3 bg-light d-flex justify-content-end gap-2 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold" id="btn-submit-text">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PILOTAGE JAVASCRIPT DE LA BOÎTE MODALE -->
<script>
function openShowModal(venueData = null) {
    var form = document.getElementById('showForm');
    var modalTitle = document.getElementById('showModalLabel');
    var methodContainer = document.getElementById('method-container');
    var submitBtnText = document.getElementById('btn-submit-text');

    var inputTitle = document.getElementById('modal_title');
    var inputCapacity = document.getElementById('modal_capacity');
    var inputDescription = document.getElementById('modal_description');

    var myModal = new bootstrap.Modal(document.getElementById('showModal'));

    if (venueData) {
        // Mode Éditon
        modalTitle.innerHTML = '<i class="bi bi-pencil-square me-2 text-warning"></i>Modifier la Salle : ' + venueData.title;
        submitBtnText.textContent = "Mettre à jour";
        form.action = "{{ url('admin/shows') }}/" + venueData.id;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';

        inputTitle.value = venueData.title;
        inputCapacity.value = venueData.total_capacity;
        inputDescription.value = venueData.description ? venueData.description : '';
    } else {
        // Mode Création
        modalTitle.innerHTML = '<i class="bi bi-plus-circle me-2 text-success"></i>Ajouter une nouvelle Salle';
        submitBtnText.textContent = "Créer la salle";
        form.action = "{{ route('admin.shows.store') }}";
        methodContainer.innerHTML = '';

        inputTitle.value = '';
        inputCapacity.value = '';
        inputDescription.value = '';
    }

    myModal.show();
}
</script>

<style>
.card-hover {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.12)!important;
}
</style>
@endsection
