@extends('layouts.app')

@section('title', 'Fichier Clients VIP & Comptes Entreprises - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-person-lines-fill text-primary me-2"></i>Fichier Clients CRM, Fidélité VIP & Entreprises
            </h3>
            <p class="text-muted small mb-0">Historique des séjours, préférences clients, statuts VIP et facturation consolidée des sociétés partenaires.</p>
        </div>
        <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newCompanyModal">
            <i class="bi bi-building-add text-warning me-1"></i> Nouvelle Entreprise Partenaire
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cartes de Synthèse CRM -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Total Clients Enregistrés</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $totalCustomers }} <small class="fs-6 fw-normal">fiches</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Clients VIP</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $vipCount }} <small class="fs-6 fw-normal">membres VIP</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Sociétés Partenaires</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $companiesCount }} <small class="fs-6 fw-normal">comptes entreprises</small></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- BLOC 1 : ENTREPRISES PARTENAIRES & RELEVÉS DE FACTURATION -->
    @if($companies->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-uppercase text-dark">
                    <i class="bi bi-briefcase-fill text-success me-2"></i>Comptes Entreprises & Facturation Différée
                </h6>
                <span class="badge bg-success-subtle text-success rounded-pill px-3">{{ $companies->count() }} société(s)</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @foreach($companies as $comp)
                        <div class="col-md-6 col-lg-4">
                            <div class="border rounded-4 p-3 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold text-primary mb-0">{{ $comp->name }}</h6>
                                        @if($comp->negotiated_discount > 0)
                                            <span class="badge bg-warning text-dark rounded-pill">-{{ $comp->negotiated_discount }}%</span>
                                        @endif
                                    </div>
                                    <div class="text-muted small mb-2">
                                        {{ $comp->tax_id ? 'NCC : ' . $comp->tax_id . ' • ' : '' }}{{ $comp->phone ?? 'Tél. non renseigné' }}
                                    </div>
                                </div>
                                <div class="pt-2 border-top d-flex justify-content-between align-items-center">
                                    <a href="{{ route('crm.index', ['company_id' => $comp->id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                        Voir employés
                                    </a>
                                    <a href="{{ route('crm.companies.statement', $comp->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                        <i class="bi bi-file-earmark-text me-1"></i> Facture Consolidée
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- BLOC 2 : FICHIER CLIENTS & FIDÉLITÉ -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <form action="{{ route('crm.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Rechercher nom, téléphone, pièce ID...">
                </div>
                <div class="col-md-3">
                    <select name="vip_level" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="vip" {{ $vipFilter == 'vip' ? 'selected' : '' }}>⭐ Clients VIP</option>
                        <option value="regular" {{ $vipFilter == 'regular' ? 'selected' : '' }}>🔹 Clients Réguliers</option>
                        <option value="standard" {{ $vipFilter == 'standard' ? 'selected' : '' }}>⚪ Clients Standard</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="company_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Toutes les entreprises</option>
                        @foreach($companies as $c)
                            <option value="{{ $c->id }}" {{ $companyId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">Filtrer</button>
                    @if($search || $vipFilter || $companyId)
                        <a href="{{ route('crm.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-2"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Client & Coordonnées</th>
                            <th>Statut & Société</th>
                            <th class="text-center">Séjours / Nuits</th>
                            <th class="text-end">CA Cumulé</th>
                            <th>Préférences</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $cust)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $cust->full_name }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="bi bi-telephone me-1"></i>{{ $cust->phone }}
                                        {{ $cust->id_card_number ? ' • ID: ' . $cust->id_card_number : '' }}
                                    </div>
                                </td>
                                <td>
                                    @if($cust->vip_level === 'vip')
                                        <span class="badge bg-warning text-dark rounded-pill px-2 fw-bold">⭐ VIP</span>
                                    @elseif($cust->vip_level === 'regular')
                                        <span class="badge bg-info-subtle text-primary rounded-pill px-2">Régulier</span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2">Standard</span>
                                    @endif

                                    @if($cust->company_name)
                                        <div class="mt-1"><span class="badge bg-primary-subtle text-primary">{{ $cust->company_name }}</span></div>
                                    @endif
                                </td>
                                <                                <td class="text-center">
                                    <strong>{{ $cust->total_stays }}</strong> séjour(s) • <span class="text-muted">{{ $cust->total_nights }} nuit(s)</span>
                                </td>
                                <td class="text-end fw-bold text-success">{{ number_format($cust->total_spent, 0, ',', ' ') }} F</td>
                                <td class="text-muted" style="max-width: 200px;">{{ $cust->preferences ?: '---' }}</td>
                                <td class="text-end pe-4">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editCustomerModal"
                                            data-customer="{{ json_encode($cust) }}">
                                        <i class="bi bi-pencil-square me-1"></i> Fiche CRM
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Aucun client trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODALE 1 : NOUVELLE ENTREPRISE PARTENAIRE -->
<div class="modal fade" id="newCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('crm.companies.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-building-add text-warning me-2"></i>Ajouter une Société Partenaire</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Raison Sociale *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: SITARAIL, ORANGE CI, BOLLORÉ..." required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">N° Compte Contribuable (NCC)</label>
                        <input type="text" name="tax_id" class="form-control" placeholder="Ex: 0102345-A">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Remise Négociée (%)</label>
                        <input type="number" name="negotiated_discount" value="0" min="0" max="100" step="1" class="form-control fw-bold">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Téléphone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">E-mail Facturation</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Adresse</label>
                    <input type="text" name="address" class="form-control" placeholder="Abidjan, Plateau...">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer la Société</button>
            </div>
        </form>
    </div>
</div>

<!-- MODALE 2 : MODIFIER LA FICHE CLIENT (VIP / SOCIÉTÉ / PRÉFÉRENCES) -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editCustomerForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-badge me-2"></i>Fiche Fidélité & Préférences Client</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nom Complet *</label>
                    <input type="text" name="full_name" id="crmFullName" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Téléphone *</label>
                        <input type="text" name="phone" id="crmPhone" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">E-mail</label>
                        <input type="email" name="email" id="crmEmail" class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">N° Pièce d'Identité</label>
                        <input type="text" name="id_card_number" id="crmIdCard" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Nationalité</label>
                        <input type="text" name="nationality" id="crmNationality" class="form-control">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-warning-emphasis">Niveau de Fidélité *</label>
                        <select name="vip_level" id="crmVipLevel" class="form-select fw-bold">
                            <option value="standard">Standard</option>
                            <option value="regular">Régulier</option>
                            <option value="vip">⭐ Client VIP</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-primary">Entreprise Rattachée</label>
                        <select name="company_id" id="crmCompanyId" class="form-select">
                            <option value="">-- Client Individuel --</option>
                            @foreach($companies as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Préférences & Notes Internes</label>
                    <textarea name="preferences" id="crmPreferences" rows="2" class="form-control" placeholder="Ex: Chambre calme, étage élevé, allergie..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer la Fiche</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editCustomerModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function (event) {
                const cust = JSON.parse(event.relatedTarget.getAttribute('data-customer'));
                document.getElementById('editCustomerForm').action = `/crm/customers/${cust.id}`;
                document.getElementById('crmFullName').value = cust.full_name || '';
                document.getElementById('crmPhone').value = cust.phone || '';
                document.getElementById('crmEmail').value = cust.email || '';
                document.getElementById('crmIdCard').value = cust.id_card_number || '';
                document.getElementById('crmNationality').value = cust.nationality || '';
                document.getElementById('crmVipLevel').value = cust.vip_level || 'standard';
                document.getElementById('crmCompanyId').value = cust.company_id || '';
                document.getElementById('crmPreferences').value = cust.preferences || '';
            });
        }
    });
</script>
@endsection

