@extends('layouts.app')

@section('title', 'Dépenses & Petite Caisse - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-wallet2 text-danger me-2"></i>Gestion des Dépenses & Petite Caisse
            </h3>
            <p class="text-muted small mb-0">Suivi des sorties de fonds, achats urgents et solde réel du tiroir-caisse en espèces.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('billing.payments') }}" class="btn btn-outline-dark rounded-pill px-3 fw-semibold">
                <i class="bi bi-journal-check me-1"></i> Grand Livre
            </a>
            <button type="button" onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-printer me-1 text-warning"></i> Imprimer État
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cartes de Synthèse Caisse Espèces du Jour -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Recettes Espèces (Aujourd'hui)</div>
                    <h3 class="fw-bold mb-0 mt-1">+ {{ number_format($todayCashIn, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Dépenses Espèces (Aujourd'hui)</div>
                    <h3 class="fw-bold mb-0 mt-1">- {{ number_format($todayCashOut, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Solde Net Tiroir-Caisse</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ number_format($netCashDrawer, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Total Dépenses (Période)</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ number_format($periodTotalExpenses, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- FORMULAIRE : NOUVEAU BON DE SORTIE -->
        <div class="col-lg-4">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 fw-bold" style="background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);">
                    <i class="bi bi-dash-circle-fill text-warning me-2"></i>Enregistrer un Décaissement
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Date de la dépense *</label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Catégorie *</label>
                            <select name="category" class="form-select form-select-sm fw-bold" required>
                                <option value="entretien">Produits d'entretien & Ménage</option>
                                <option value="cuisine">Achats urgents Cuisine / Bar</option>
                                <option value="carburant">Carburant Groupe Électrogène / Véhicule</option>
                                <option value="maintenance">Dépannage & Petit Matériel</option>
                                <option value="fournitures">Fournitures Bureau & Réception</option>
                                <option value="autre">Autre dépense diverse</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Motif précis de la dépense *</label>
                            <input type="text" name="description" class="form-control form-control-sm" placeholder="Ex: Achat 2 bouteilles de gaz, Eau de javel, Gasoil..." required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small text-danger">Montant (FCFA) *</label>
                               <input type="number" name="amount" min="0" step="1" class="form-control fw-bold" placeholder="Ex: 7500" required>

                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Mode de sortie *</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Espèces">Espèces (Tiroir)</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Orange Money">Orange Money</option>
                                    <option value="MTN Money">MTN Money</option>
                                    <option value="Virement">Virement / Chèque</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Bénéficiaire / Fournisseur</label>
                            <input type="text" name="beneficiary" class="form-control form-control-sm" placeholder="Ex: Station Total, Supermarché, Technicien...">
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">N° Reçu / Justificatif</label>
                            <input type="text" name="reference" class="form-control form-control-sm" placeholder="Ex: Facture N° 458">
                        </div>

                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow">
                            <i class="bi bi-check2-circle me-1"></i> Valider la Sortie de Caisse
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TABLEAU : HISTORIQUE DES DÉPENSES -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <form action="{{ route('expenses.index') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Du</label>
                            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold mb-1">Au</label>
                            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-1">Catégorie</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Toutes les catégories</option>
                                <option value="entretien" {{ $category == 'entretien' ? 'selected' : '' }}>Entretien & Ménage</option>
                                <option value="cuisine" {{ $category == 'cuisine' ? 'selected' : '' }}>Cuisine / Bar</option>
                                <option value="carburant" {{ $category == 'carburant' ? 'selected' : '' }}>Carburant</option>
                                <option value="maintenance" {{ $category == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="fournitures" {{ $category == 'fournitures' ? 'selected' : '' }}>Fournitures</option>
                                <option value="autre" {{ $category == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">Filtrer</button>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4">Bon N° / Date</th>
                                    <th>Motif & Bénéficiaire</th>
                                    <th>Mode</th>
                                    <th class="text-end">Montant</th>
                                    <th>Agent</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $exp)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $exp->voucher_number }}</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary-subtle text-dark text-uppercase mb-1" style="font-size: 0.65rem;">{{ $exp->category }}</span>
                                            <div class="fw-semibold text-dark">{{ $exp->description }}</div>
                                            @if($exp->beneficiary || $exp->reference)
                                                <div class="text-muted" style="font-size: 0.72rem;">
                                                    {{ $exp->beneficiary }} {{ $exp->reference ? '• Réf: ' . $exp->reference : '' }}
                                                </div>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $exp->payment_method }}</span></td>
                                        <td class="text-end fw-bold text-danger">- {{ number_format($exp->amount, 0, ',', ' ') }} F</td>
                                        <td class="text-muted">{{ $exp->author_name ?? 'Système' }}</td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary rounded-circle px-2 py-0 me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editExpenseModal"
                                                    data-expense="{{ json_encode($exp) }}"
                                                    title="Modifier ce bon">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce bon de dépense ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle px-2 py-0" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Aucune dépense enregistrée sur cette période.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- MODALE : MODIFIER UN BON DE DÉPENSE -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editExpenseForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Modifier le Bon <span id="editVoucherLabel" class="badge bg-warning text-dark ms-1"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Catégorie *</label>
                        <select name="category" id="editExpCategory" class="form-select form-select-sm" required>
                            <option value="approvisionnement">Approvisionnement Cuisine / Bar</option>
                            <option value="entretien">Produits Entretien / Ménage</option>
                            <option value="maintenance">Maintenance & Réparations</option>
                            <option value="carburant">Carburant / Groupe Électrogène</option>
                            <option value="facture">Facture Eau / Électricité / Internet</option>
                            <option value="autre">Autre Dépense / Acompte</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Date *</label>
                        <input type="date" name="expense_date" id="editExpDate" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">Libellé / Motif de la dépense *</label>
                    <input type="text" name="description" id="editExpDescription" class="form-control form-control-sm" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Montant (FCFA) *</label>
                        <input type="number" name="amount" id="editExpAmount" min="0" step="1" class="form-control form-control-sm fw-bold" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Mode de sortie *</label>
                        <select name="payment_method" id="editExpMethod" class="form-select form-select-sm" required>
                            <option value="Espèces">Espèces (Tiroir-caisse)</option>
                            <option value="Wave">Wave</option>
                            <option value="Orange Money">Orange Money</option>
                            <option value="MTN MoMo">MTN MoMo</option>
                            <option value="Virement / Chèque">Virement / Chèque</option>
                        </select>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Bénéficiaire / Fournisseur</label>
                        <input type="text" name="beneficiary" id="editExpBeneficiary" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">N° Reçu / Référence</label>
                        <input type="text" name="reference" id="editExpReference" class="form-control form-control-sm">
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
        const editExpenseModal = document.getElementById('editExpenseModal');
        if (editExpenseModal) {
            editExpenseModal.addEventListener('show.bs.modal', function (event) {
                const exp = JSON.parse(event.relatedTarget.getAttribute('data-expense'));
                document.getElementById('editExpenseForm').action = `/expenses/${exp.id}`;
                document.getElementById('editVoucherLabel').textContent = exp.voucher_number || '';
                document.getElementById('editExpCategory').value = exp.category || 'autre';
                document.getElementById('editExpDate').value = exp.expense_date ? exp.expense_date.substring(0, 10) : '';
                document.getElementById('editExpDescription').value = exp.description || '';
                document.getElementById('editExpAmount').value = Math.round(exp.amount || 0);
                document.getElementById('editExpMethod').value = exp.payment_method || 'Espèces';
                document.getElementById('editExpBeneficiary').value = exp.beneficiary || '';
                document.getElementById('editExpReference').value = exp.reference || '';
            });
        }
    });
</script>


@endsection
