@extends('layouts.app')

@section('title', 'État des Salles de Spectacle - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-building-fill text-primary me-2"></i>État des Salles de Spectacle
        </h1>
        <p class="text-muted small mb-0 mt-1">Configuration des espaces événementiels, auditoriums et capacités d'accueil</p>
    </div>
    <!-- Ce bouton ouvre instantanément la fenêtre modale en mode Création -->
    <button type="button" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#showModal" data-mode="create" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); border: none;">
        <i class="bi bi-plus-circle-fill text-warning me-2"></i>Ajouter une Salle
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Erreur de saisie :</div>
        @foreach($errors->all() as $error)
            <div class="small">• {{ $error }}</div>
        @endforeach
    </div>
@endif

@php
    $totalVenues   = $venues->count();
    $totalCapacity = $venues->sum('total_capacity');
    $gradients = [
        'linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%)', // Bleu Royal
        'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)', // Pourpre & Magenta
        'linear-gradient(135deg, #065f46 0%, #10b981 100%)', // Émeraude
        'linear-gradient(135deg, #b45309 0%, #f59e0b 100%)', // Ambre Doré
    ];
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DES INFRASTRUCTURES                             -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow rounded-4 text-white h-100" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Infrastructures Actives</span>
                    <h2 class="fw-bold mb-0">{{ $totalVenues }} <small class="fs-6 fw-normal">salle(s) configurée(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-buildings-fill fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow rounded-4 text-white h-100" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Capacité Totale d'Accueil</span>
                    <h2 class="fw-bold mb-0">{{ number_format($totalCapacity, 0, ',', ' ') }} <small class="fs-6 fw-normal">places assises</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-people-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. GRILLE D'AFFICHAGE COLORÉE DES SALLES (TABLE 'SHOWS')              -->
<!-- ===================================================================== -->
<div class="row g-4">
    @forelse($venues as $index => $venue)
        @php
            $headerGradient = $gradients[$index % count($gradients)];
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow border-0 rounded-4 overflow-hidden venue-card-hover">
                <!-- Bandeau supérieur coloré -->
                <div class="card-header text-white py-3 px-4 border-0 d-flex justify-content-between align-items-center" style="background: {{ $headerGradient }};">
                    <h5 class="card-title fw-bold mb-0 text-truncate pe-2">
                        <i class="bi bi-mic-fill text-warning me-2"></i>{{ $venue->title }}
                    </h5>
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold shadow-sm flex-shrink-0">
                        <i class="bi bi-people-fill text-primary me-1"></i>{{ number_format($venue->total_capacity, 0, ',', ' ') }}
                    </span>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between bg-white">
                    <div>
                        <div class="mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 small fw-bold">
                                Capacité : {{ number_format($venue->total_capacity, 0, ',', ' ') }} places
                            </span>
                        </div>
                        <p class="card-text text-muted small mt-3 mb-4" style="line-height: 1.6;">
                            {{ $venue->description ?? 'Aucune description fournie pour cette infrastructure.' }}
                        </p>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-between align-items-center">
                        <span class="text-success small fw-bold">
                            <i class="bi bi-check-circle-fill me-1"></i>Opérationnelle
                        </span>
                        <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-bold shadow-sm"
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
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                <div class="text-primary mb-3"><i class="bi bi-building fs-1"></i></div>
                <h5 class="fw-bold text-dark">Aucune salle de spectacle configurée</h5>
                <p class="text-muted small mb-0">Cliquez sur le bouton « Ajouter une Salle » en haut à droite pour créer votre premier espace.</p>
            </div>
        </div>
    @endforelse
</div>

<!-- ===================================================================== -->
<!-- 3. FENÊTRE MODALE UNIQUE POUR LA CRÉATION ET LA MODIFICATION          -->
<!-- ===================================================================== -->
<div class="modal fade" id="showModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #1e3a8a 0%, #4f46e5 100%);">
                <h5 class="modal-title fw-bold" id="showModalLabel">
                    <i class="bi bi-sliders text-warning me-2"></i>Gestion de la Salle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="showForm" method="POST" action="">
                @csrf
                <div id="method-container"></div>

                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <!-- Nom de la Salle -->
                    <div class="mb-3">
                        <label for="modal_title" class="form-label fw-bold text-dark small text-uppercase">Nom / Titre de la Salle</label>
                        <input type="text" class="form-control fw-bold border-primary-subtle" id="modal_title" name="title" placeholder="Ex: Salle de Concert Opéra" required>
                    </div>

                    <!-- Capacité Maximale -->
                    <div class="mb-3">
                        <label for="modal_capacity" class="form-label fw-bold text-dark small text-uppercase">Capacité Totale (Places)</label>
                        <input type="number" class="form-control fw-bold text-primary border-primary-subtle" id="modal_capacity" name="total_capacity" min="1" placeholder="Ex: 500" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-0">
                        <label for="modal_description" class="form-label fw-bold text-dark small text-uppercase">Description (Optionnel)</label>
                        <textarea class="form-control border-primary-subtle" id="modal_description" name="description" rows="3" placeholder="Équipements, acoustique, agencement..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top p-3 bg-white d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn text-white px-4 rounded-pill fw-bold shadow-sm" id="btn-submit-text" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .venue-card-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .venue-card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.15) !important;
    }
</style>

{{-- CONSERVEZ VOTRE BLOC <script> ... </script>@endsection EXISTANT CI-DESSOUS --}}


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
