@extends('layouts.app')

@section('title', 'Activités & Services Annexes - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-water text-primary me-2"></i>Activités & Services Annexes
            </h3>
            <p class="text-muted small mb-0">Pass Piscine, Spa & Bien-être, Navettes Aéroport & Boutique.</p>
        </div>
        <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newCatalogModal">
            <i class="bi bi-plus-circle-fill text-warning me-1"></i> Nouvelle Offre au Catalogue
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- COLONNE GAUCHE : GUICHET DE VENTE / RÉSERVATION D'ACTIVITÉ -->
        <div class="col-lg-5">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 fw-bold" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                    <i class="bi bi-cart-plus-fill text-warning me-2"></i>Enregistrer une Prestation
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('activities.book') }}" method="POST">
                        @csrf

                        <!-- Choix de la prestation -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Prestation / Activité *</label>
                            <select name="hotel_activity_id" id="activitySelect" class="form-select fw-bold" required>
                                <option value="" data-price="0">-- Sélectionner une activité --</option>
                                @foreach($activities as $act)
                                    <option value="{{ $act->id }}" data-price="{{ $act->price }}">
                                        [{{ strtoupper($act->category) }}] {{ $act->name }} — {{ number_format($act->price, 0, ',', ' ') }} F / {{ $act->unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantité & Total estimé -->
                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label class="form-label fw-bold small">Quantité *</label>
                                <input type="number" name="quantity" id="activityQty" value="1" min="1" class="form-control fw-bold" required>
                            </div>
                            <div class="col-7">
                                <label class="form-label fw-bold small">Montant Total</label>
                                <div class="form-control bg-light fw-bold text-primary text-end" id="activityTotalDisplay">0 F</div>
                            </div>
                        </div>

                        <!-- Mode de facturation : Comptoir immédiat vs Sur Chambre -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small d-block">Mode de facturation *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="billing_mode" id="modeDirect" value="direct" checked>
                                <label class="btn btn-outline-success fw-semibold" for="modeDirect">
                                    <i class="bi bi-cash-coin me-1"></i> Paiement Immédiat
                                </label>

                                <input type="radio" class="btn-check" name="billing_mode" id="modeRoom" value="room">
                                <label class="btn btn-outline-primary fw-semibold" for="modeRoom">
                                    <i class="bi bi-door-closed me-1"></i> Imputer sur Chambre
                                </label>
                            </div>
                        </div>

                        <!-- Bloc Paiement Immédiat (Client Externe) -->
                        <div id="directPaymentBox">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Nom du client (Optionnel)</label>
                                <input type="text" name="client_name" class="form-control form-control-sm" placeholder="Ex: Client Externe / Pass Piscine">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Mode d'encaissement *</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Espèces">Espèces</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Orange Money">Orange Money</option>
                                    <option value="MTN Money">MTN Money</option>
                                    <option value="Carte Bancaire">Carte Bancaire</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bloc Imputation sur Chambre Occupée -->
                        <div id="roomSelectionBox" class="d-none">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-primary">Chambre Occupée *</label>
                                <select name="room_id" class="form-select border-primary fw-bold">
                                    <option value="">-- Choisir la chambre --</option>
                                    @foreach($occupiedRooms as $r)
                                        <option value="{{ $r->id }}">Chambre {{ $r->number }} ({{ $r->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Notes / Détails -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Observation / Créneau horaire</label>
                            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Ex: Vol Air Côte d'Ivoire 18h30, Massage 15h00...">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                            <i class="bi bi-check2-circle me-1"></i> Valider la Prestation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : HISTORIQUE DES ACTIVITÉS & CATALOGUE -->
        <div class="col-lg-7">
            <!-- Tableau des dernières ventes / imputations -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-uppercase"><i class="bi bi-clock-history text-warning me-2"></i>Dernières Prestations Enregistrées</span>
                    <span class="badge bg-warning text-dark rounded-pill">{{ $bookings->count() }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4">Date</th>
                                    <th>Prestation</th>
                                    <th>Client / Chambre</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center pe-4">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                                                @forelse($bookings as $b)
                                    <tr>
                                        <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse($b->created_at)->format('d/m H:i') }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $b->activity_name }} (x{{ $b->quantity }})</div>
                                            @if($b->notes)
                                                <div class="text-muted" style="font-size: 0.72rem;">{{ $b->notes }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($b->room_number)
                                                <span class="badge bg-primary">Ch. {{ $b->room_number }}</span>
                                            @else
                                                <span class="fw-semibold text-secondary">{{ $b->client_name ?? 'Externe' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-dark">{{ number_format($b->total_amount, 0, ',', ' ') }} F</td>
                                        <td class="text-center pe-4">
                                            @if($b->payment_status === 'paid')
                                                <span class="badge bg-success-subtle text-success rounded-pill px-2">Payé</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-dark rounded-pill px-2">En cours Ch.</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Aucune activité enregistrée pour le moment.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aperçu rapide du catalogue avec boutons Modifier / Supprimer -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light py-2 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-uppercase text-muted">Tarifs du Catalogue Actif</span>
                    <span class="badge bg-secondary-subtle text-dark rounded-pill">{{ $activities->count() }} offre(s)</span>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        @foreach($activities as $act)
                            <div class="col-md-6">
                                <div class="border rounded-3 p-2 px-3 d-flex justify-content-between align-items-center bg-white shadow-sm h-100">
                                    <div>
                                        <span class="badge bg-primary-subtle text-primary text-uppercase" style="font-size: 0.65rem;">{{ $act->category }}</span>
                                        <div class="fw-semibold small text-dark mt-1">{{ $act->name }}</div>
                                        <div class="fw-bold text-success small">
                                            {{ number_format($act->price, 0, ',', ' ') }} F <span class="text-muted fw-normal">/ {{ $act->unit }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 ms-2">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editCatalogModal"
                                                data-id="{{ $act->id }}"
                                                data-category="{{ $act->category }}"
                                                data-name="{{ $act->name }}"
                                                data-price="{{ $act->price }}"
                                                data-unit="{{ $act->unit }}"
                                                title="Modifier cette offre">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('activities.catalog.destroy', $act->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer définitivement « {{ addslashes($act->name) }} » du catalogue ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fenêtre Modale : Ajouter une Offre au Catalogue -->
<div class="modal fade" id="newCatalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('activities.catalog.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-warning me-2"></i>Nouvelle Offre au Catalogue
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catégorie *</label>
                    <select name="category" class="form-select" required>
                        <option value="piscine">Piscine & Transats</option>
                        <option value="spa">Spa & Bien-être</option>
                        <option value="navette">Navette & Location Véhicule</option>
                        <option value="boutique">Boutique & Divers</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nom de la prestation *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Pass Piscine Enfant, Location 4x4 Journée..." required>
                </div>
                <div class="row g-2">
                    <div class="col-7">
                        <label class="form-label fw-bold small">Tarif (FCFA) *</label>
                        <input type="number" name="price" min="0" step="500" class="form-control fw-bold" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-bold small">Unité *</label>
                        <input type="text" name="unit" value="forfait" class="form-control" placeholder="personne, heure, trajet..." required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer au Catalogue</button>
            </div>
        </form>
    </div>
</div>

<!-- Fenêtre Modale : Modifier une Offre du Catalogue -->
<div class="modal fade" id="editCatalogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCatalogForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Modifier l'Offre du Catalogue
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catégorie *</label>
                    <select name="category" id="editCatCategory" class="form-select" required>
                        <option value="piscine">Piscine & Transats</option>
                        <option value="spa">Spa & Bien-être</option>
                        <option value="navette">Navette & Location Véhicule</option>
                        <option value="boutique">Boutique & Divers</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nom de la prestation *</label>
                    <input type="text" name="name" id="editCatName" class="form-control" required>
                </div>
                <div class="row g-2">
                    <div class="col-7">
                        <label class="form-label fw-bold small">Tarif (FCFA) *</label>
                        <input type="number" name="price" id="editCatPrice" min="0" step="500" class="form-control fw-bold" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-bold small">Unité *</label>
                        <input type="text" name="unit" id="editCatUnit" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activitySelect = document.getElementById('activitySelect');
        const qtyInput = document.getElementById('activityQty');
        const totalDisplay = document.getElementById('activityTotalDisplay');
        const modeDirect = document.getElementById('modeDirect');
        const modeRoom = document.getElementById('modeRoom');
        const directBox = document.getElementById('directPaymentBox');
        const roomBox = document.getElementById('roomSelectionBox');

        function updateActivityTotal() {
            const selectedOption = activitySelect.options[activitySelect.selectedIndex];
            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const qty = parseInt(qtyInput.value) || 1;
            const total = price * qty;
            totalDisplay.textContent = new Intl.NumberFormat('fr-FR').format(total) + ' F';
        }

        function toggleBillingMode() {
            if (modeRoom.checked) {
                directBox.classList.add('d-none');
                roomBox.classList.remove('d-none');
            } else {
                roomBox.classList.add('d-none');
                directBox.classList.remove('d-none');
            }
        }

        activitySelect.addEventListener('change', updateActivityTotal);
        qtyInput.addEventListener('input', updateActivityTotal);
        modeDirect.addEventListener('change', toggleBillingMode);
        modeRoom.addEventListener('change', toggleBillingMode);

        // Pré-remplir la modale d'édition du catalogue
        const editModal = document.getElementById('editCatalogModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');
                document.getElementById('editCatalogForm').action = `/activities/catalog/${id}`;
                document.getElementById('editCatCategory').value = btn.getAttribute('data-category');
                document.getElementById('editCatName').value = btn.getAttribute('data-name');
                document.getElementById('editCatPrice').value = btn.getAttribute('data-price');
                document.getElementById('editCatUnit').value = btn.getAttribute('data-unit');
            });
        }
    });
</script>
@endsection
