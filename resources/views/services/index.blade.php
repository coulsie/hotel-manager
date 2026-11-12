@extends('layouts.app')

@section('title', 'Pressing & Lavage Auto - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom gap-3">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-droplet-half text-info me-2"></i>Pressing & <span class="text-primary">Lavage Auto</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Blanchisserie, entretien des véhicules et facturation clients externes ou chambres</p>
    </div>

    <button type="button" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#catalogModal" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border: none;">
        <i class="bi bi-tags-fill text-warning me-2"></i>Gérer les Tarifs ({{ $catalogs->count() }})
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ session('error') }}</strong>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3">
        @foreach($errors->all() as $error)
            <div class="small">• {{ $error }}</div>
        @endforeach
    </div>
@endif

@php
    $countInProgress = $services->where('work_status', 'pending')->count();
    $countReady      = $services->where('work_status', 'ready')->count();
    $totalUnpaidExt  = $services->whereNull('room_id')->where('payment_status', 'unpaid')->sum('total_amount');
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE D'ATELIER (DÉGRADÉS ÉCLATANTS)                  -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">En Cours de Traitement</span>
                    <h2 class="fw-bold mb-0">{{ $countInProgress }} <small class="fs-6 fw-normal">dossier(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-hourglass-split fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Prêts / Terminés</span>
                    <h2 class="fw-bold mb-0">{{ $countReady }} <small class="fs-6 fw-normal">dossier(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-check2-all fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">À Encaisser (Externes)</span>
                    <h2 class="fw-bold mb-0">{{ number_format($totalUnpaidExt, 0, ',', ' ') }} F</h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-car-front-fill fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- ===================================================================== -->
    <!-- 2. COLONNE GAUCHE : FORMULAIRE DE DÉPÔT / PRISE EN CHARGE             -->
    <!-- ===================================================================== -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #0891b2 0%, #0284c7 100%);">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-plus-circle-fill text-warning me-2"></i>Nouvelle Prise en Charge
                </h5>
            </div>

            <div class="card-body p-4" style="background-color: #f0f9ff;">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf

                    <!-- Choix de la prestation -->
                    <div class="mb-3">
                        <label for="catalog_id" class="form-label fw-bold text-dark small text-uppercase">Prestation (Pressing / Auto)</label>
                        <select class="form-select fw-bold text-dark border-info-subtle" id="catalog_id" name="catalog_id" required>
                            <option value="" selected disabled>Choisir dans la grille...</option>
                            @foreach($catalogs as $cat)
                                <option value="{{ $cat->id }}" data-price="{{ $cat->price }}">
                                    {{ $cat->category === 'pressing' ? '👔 [PRESSING]' : '🚗 [LAVAGE AUTO]' }} {{ $cat->name }} — {{ number_format($cat->price, 0, ',', ' ') }} F
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantité -->
                    <div class="mb-3">
                        <label for="quantity" class="form-label fw-bold text-dark small text-uppercase">Quantité (Pièces / Véhicules)</label>
                        <input type="number" class="form-control fw-bold text-center text-primary border-info-subtle" id="quantity" name="quantity" min="1" value="1" required>
                    </div>

                    <!-- Type de client : Extérieur ou Chambre -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small text-uppercase d-block">Type de Client</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="w-100 p-2 bg-white rounded-3 border border-primary-subtle shadow-sm d-flex align-items-center gap-2" for="type_ext" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="client_type" id="type_ext" value="external" checked onclick="toggleClientType('external')">
                                    <span class="fw-bold text-primary small">🚶 Extérieur</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="w-100 p-2 bg-white rounded-3 border border-danger-subtle shadow-sm d-flex align-items-center gap-2" for="type_room" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="client_type" id="type_room" value="room" onclick="toggleClientType('room')">
                                    <span class="fw-bold text-danger small">🚪 Chambre</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Bloc Client Extérieur -->
                    <div id="external_client_box" class="p-3 bg-white rounded-3 border border-primary-subtle mb-3">
                        <label for="customer_name" class="form-label fw-bold text-primary small text-uppercase">Nom du Client / Plaque Auto</label>
                        <input type="text" class="form-control fw-semibold mb-3" id="customer_name" name="customer_name" placeholder="Ex: M. Touré / AA-123-CI" required>

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="pay_now" name="pay_now" value="1" onchange="togglePayMethod()">
                            <label class="form-check-label fw-bold text-success small" for="pay_now">Encaisser immédiatement au dépôt</label>
                        </div>

                        <div id="pay_method_box" class="mt-2 d-none">
                            <select class="form-select form-select-sm fw-bold" name="payment_method">
                                <option value="cash">💵 Espèces (Cash)</option>
                                <option value="wave">🧡 Wave</option>
                                <option value="orange_money">🍊 Orange Money</option>
                                <option value="mtn_momo">💛 MTN MoMo</option>
                                <option value="card">💳 Carte Bancaire</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bloc Client Chambre -->
                    <div id="room_client_box" class="p-3 rounded-3 border border-danger d-none mb-3" style="background-color: #fef2f2;">
                        <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">Chambre occupée à débiter</label>
                        <select class="form-select fw-bold border-danger-subtle" id="room_id" name="room_id">
                            <option value="" selected disabled>Sélectionner la chambre...</option>
                            @foreach($occupiedRooms as $r)
                                <option value="{{ $r->id }}">🚪 Chambre {{ $r->number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted d-block mt-1">Le montant s'ajoutera automatiquement à la facture de la chambre.</small>
                    </div>

                    <div class="p-3 bg-white rounded-3 border border-success-subtle d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold small text-uppercase text-secondary">Total Prestation :</span>
                        <span class="fs-5 fw-bold text-success" id="service-total-preview">0 F CFA</span>
                    </div>

                    <button type="submit" class="btn btn-lg w-100 rounded-pill fw-bold text-white shadow" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                        <i class="bi bi-check-circle-fill me-2"></i>Enregistrer le Dépôt
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 3. COLONNE DROITE : SUIVI D'ATELIER & RÈGLEMENTS (8 COLONNES)         -->
    <!-- ===================================================================== -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <span><i class="bi bi-list-check text-warning me-2 fs-5"></i>Suivi des Prestations Pressing & Lavage Auto</span>
                <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ $services->count() }} opération(s)</span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="small text-uppercase" style="background-color: #eff6ff; color: #1e40af;">
                            <tr>
                                <th class="ps-3 py-3">Date / Pôle</th>
                                <th class="py-3">Prestation</th>
                                <th class="py-3">Client / Chambre</th>
                                <th class="text-end py-3">Montant</th>
                                <th class="text-center py-3">Atelier</th>
                                <th class="text-center pe-3 py-3">Règlement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $srv)
                                <tr>
                                    <td class="ps-3">
                                        <div class="small text-muted">{{ $srv->created_at->format('d/m H:i') }}</div>
                                        @if($srv->category === 'pressing')
                                            <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">👔 PRESSING</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">🚗 LAVAGE AUTO</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $srv->service_name }}</div>
                                        <small class="text-muted">Qté : {{ $srv->quantity }} × {{ number_format($srv->unit_price, 0, ',', ' ') }} F</small>
                                    </td>
                                    <td>
                                        @if($srv->room)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-bold">
                                                🚪 Chambre {{ $srv->room->number }}
                                            </span>
                                        @else
                                            <span class="fw-bold text-dark small"><i class="bi bi-person-fill text-primary me-1"></i>{{ $srv->customer_name }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-dark">{{ number_format($srv->total_amount, 0, ',', ' ') }} F</td>
                                    <td class="text-center">
                                        @if($srv->work_status === 'pending')
                                            <form action="{{ route('services.ready', $srv->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill fw-bold px-2" title="Cliquer quand terminé">
                                                    <i class="bi bi-hourglass-split me-1"></i>En cours
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge bg-success rounded-pill px-3 py-1 fw-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i>Prêt
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        @if($srv->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">
                                                Soldé
                                            </span>
                                        @elseif($srv->room_id)
                                            <span class="badge bg-secondary-subtle text-dark rounded-pill px-2 py-1 small">
                                                Sur note chambre
                                            </span>
                                        @else
                                            <form action="{{ route('services.pay', $srv->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="payment_method" value="cash">
                                                <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm">
                                                    <i class="bi bi-cash me-1"></i>Encaisser
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small">
                                        Aucune prestation pressing ou lavage auto enregistrée pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 4. MODALE DE GESTION DE LA GRILLE TARIFAIRE                           -->
<!-- ===================================================================== -->
<div class="modal fade" id="catalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <h5 class="modal-title fw-bold"><i class="bi bi-tags-fill text-warning me-2"></i>Grille Tarifaire Pressing & Lavage Auto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Formulaire d'ajout d'un nouveau tarif -->
                <form action="{{ route('services.catalog.store') }}" method="POST" class="row g-2 mb-4 p-3 bg-light rounded-4 border">
                    @csrf
                    <div class="col-md-3">
                        <select name="category" class="form-select fw-bold" required>
                            <option value="pressing">👔 Pressing</option>
                            <option value="car_wash">🚗 Lavage Auto</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="name" class="form-control fw-semibold" placeholder="Ex: Costume 2 pièces / Lavage SUV" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price" class="form-control fw-bold text-success" placeholder="Prix (F)" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100 fw-bold rounded-pill">Ajouter</button>
                    </div>
                </form>

                <!-- Tableau des tarifs existants -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Pôle</th>
                                <th>Désignation</th>
                                <th class="text-end">Tarif</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($catalogs as $cat)
                                <!-- 1. LIGNE D'AFFICHAGE STANDARD -->
                                <tr id="row-view-{{ $cat->id }}">
                                    <td>
                                        <span class="badge {{ $cat->category === 'pressing' ? 'bg-info text-dark' : 'bg-primary' }}">
                                            {{ $cat->category === 'pressing' ? 'Pressing' : 'Lavage Auto' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ $cat->name }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($cat->price, 0, ',', ' ') }} F</td>
                                    <td class="text-end text-nowrap">
                                        <!-- Bouton Modifier -->
                                        <button type="button"
                                                onclick="toggleEditCatalog({{ $cat->id }})"
                                                class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2 me-1"
                                                title="Modifier le tarif">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <!-- Bouton Supprimer -->
                                        <form action="{{ route('services.catalog.destroy', $cat->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Retirer ce tarif du catalogue ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill py-0 px-2" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- 2. LIGNE D'ÉDITION (MASQUÉE PAR DÉFAUT) -->
                                <tr id="row-edit-{{ $cat->id }}" class="d-none table-warning">
                                    <td colspan="4" class="p-2">
                                        <form action="{{ route('services.catalog.update', $cat->id) }}" method="POST" class="row g-2 align-items-center m-0">
                                            @csrf
                                            @method('PUT')
                                            <div class="col-md-3">
                                                <select name="category" class="form-select form-select-sm fw-bold" required>
                                                    <option value="pressing" {{ $cat->category === 'pressing' ? 'selected' : '' }}>👔 Pressing</option>
                                                    <option value="car_wash" {{ $cat->category === 'car_wash' ? 'selected' : '' }}>🚗 Lavage Auto</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="name" value="{{ $cat->name }}" class="form-control form-control-sm fw-semibold" required>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="price" value="{{ intval($cat->price) }}" class="form-control fw-bold text-success text-end" min="0" required>
                                                    <span class="input-group-text fw-bold">F</span>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-end text-nowrap">
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill py-0 px-2 me-1" title="Enregistrer">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                                <button type="button" onclick="toggleEditCatalog({{ $cat->id }})" class="btn btn-sm btn-secondary rounded-pill py-0 px-2" title="Annuler">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted small">Aucun tarif configuré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script pour basculer entre l'affichage et la modification d'une ligne -->
<script>
    function toggleEditCatalog(id) {
        const viewRow = document.getElementById('row-view-' + id);
        const editRow = document.getElementById('row-edit-' + id);

        viewRow.classList.toggle('d-none');
        editRow.classList.toggle('d-none');
    }
</script>


<!-- SCRIPT INTERACTIF POUR LA PRISE EN CHARGE -->
<script>
function toggleClientType(type) {
    const extBox = document.getElementById('external_client_box');
    const roomBox = document.getElementById('room_client_box');
    const custInput = document.getElementById('customer_name');
    const roomInput = document.getElementById('room_id');

    if (type === 'external') {
        extBox.classList.remove('d-none');
        roomBox.classList.add('d-none');
        custInput.setAttribute('required', 'required');
        roomInput.removeAttribute('required');
        roomInput.value = '';
    } else {
        extBox.classList.add('d-none');
        roomBox.classList.remove('d-none');
        custInput.removeAttribute('required');
        custInput.value = '';
        roomInput.setAttribute('required', 'required');
    }
}

function togglePayMethod() {
    const payNow = document.getElementById('pay_now').checked;
    const methodBox = document.getElementById('pay_method_box');
    if (payNow) {
        methodBox.classList.remove('d-none');
    } else {
        methodBox.classList.add('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const catSelect = document.getElementById('catalog_id');
    const qtyInput = document.getElementById('quantity');
    const preview = document.getElementById('service-total-preview');

    function updatePreview() {
        const opt = catSelect.options[catSelect.selectedIndex];
        const price = opt && opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
        const qty = parseInt(qtyInput.value) || 1;
        preview.textContent = new Intl.NumberFormat('fr-FR').format(price * qty) + ' F CFA';
    }

    catSelect.addEventListener('change', updatePreview);
    qtyInput.addEventListener('input', updatePreview);
});
</script>
@endsection
