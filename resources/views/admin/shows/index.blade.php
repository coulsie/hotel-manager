@extends('layouts.app')

@section('title', 'État des Salles de Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-building me-2 text-primary"></i>État des Salles de Spectacle</h1>
    <!-- Ce bouton ouvre instantanément la fenêtre modale en mode Création -->
   <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#showModal" data-mode="create">
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
                        <!-- Correction de la variable : $venue au lieu de $show -->
                        <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 fw-bold"
                                data-bs-toggle="modal"
                                data-bs-target="#showModal"
                                data-mode="edit"
                                data-show="{{ json_encode($venue) }}">
                            <i class="bi bi-pencil-square me-1"></i> Modifier
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
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('showModal');
    if (!modalElement) return;

    modalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const mode = button.getAttribute('data-mode');
        const form = modalElement.querySelector('form');
        const modalTitle = modalElement.querySelector('.modal-title');
        const submitBtn = modalElement.querySelector('button[type="submit"]');

        // Suppression de l'ancien champ caché _method (PUT) s'il existe
        const existingMethod = form.querySelector('input[name="_method"]');
        if (existingMethod) existingMethod.remove();

        if (mode === 'edit') {
            const showData = JSON.parse(button.getAttribute('data-show'));

            if (modalTitle) modalTitle.innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i>Modifier la salle : ' + showData.title;
            if (submitBtn) {
                submitBtn.textContent = 'Mettre à jour';
                submitBtn.className = 'btn btn-warning text-dark rounded-pill px-4 fw-bold';
            }

            form.action = "{{ url('admin/shows') }}/" + showData.id;

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'PUT';
            form.appendChild(methodInput);

            // Remplissage automatique des champs selon leur attribut name
            if (form.querySelector('[name="title"]')) form.querySelector('[name="title"]').value = showData.title || '';
            if (form.querySelector('[name="total_capacity"]')) form.querySelector('[name="total_capacity"]').value = showData.total_capacity || '';
            if (form.querySelector('[name="description"]')) form.querySelector('[name="description"]').value = showData.description || '';
        } else {
            if (modalTitle) modalTitle.innerHTML = '<i class="bi bi-plus-circle text-info me-2"></i>Ajouter une nouvelle salle';
            if (submitBtn) {
                submitBtn.textContent = 'Enregistrer la salle';
                submitBtn.className = 'btn btn-primary rounded-pill px-4 fw-bold';
            }

            form.action = "{{ route('admin.shows.store') }}";
            form.reset();
        }
    });
});
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
