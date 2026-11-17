@extends('layouts.app')

@section('title', 'Gestion des Stocks & Économat - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-seam-fill text-primary me-2"></i>Gestion des Stocks & Économat
            </h3>
            <p class="text-muted small mb-0">Suivi des produits Bar, Cuisine, Ménage, Linge & Maintenance avec alertes de seuil critique.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newItemModal">
                <i class="bi bi-plus-circle-fill text-warning me-1"></i> Nouvel Article
            </button>
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-printer me-1"></i> Inventaire
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cartes de Synthèse Économat -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Références en Stock</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $totalItems }} <small class="fs-6 fw-normal">article(s)</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, {{ $lowStockCount > 0 ? '#dc2626 0%, #ef4444 100%' : '#059669 0%, #10b981 100%' }});">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Alertes Stock Critique</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $lowStockCount }} <small class="fs-6 fw-normal">produit(s) sous seuil</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Valorisation Globale Stock</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalValuation, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- COLONNE GAUCHE : FORMULAIRE MOUVEMENT DE STOCK (ENTRÉE / SORTIE / PERTE) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
                <div class="card-header text-white py-3 px-4 fw-bold" style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
                    <i class="bi bi-arrow-left-right text-warning me-2"></i>Mouvement de Stock
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('stock.movements.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Article concerné *</label>
                            <select name="stock_item_id" class="form-select form-select-sm fw-bold" required>
                                <option value="">-- Sélectionner un article --</option>
                                @foreach($allItems as $it)
                                    <option value="{{ $it->id }}">
                                        [{{ strtoupper($it->category) }}] {{ $it->name }} (Dispo : {{ $it->current_stock }} {{ $it->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small d-block">Type d'opération *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="type" id="typeIn" value="in" checked>
                                <label class="btn btn-outline-success btn-sm fw-bold" for="typeIn">
                                    <i class="bi bi-plus-lg"></i> Entrée
                                </label>

                                <input type="radio" class="btn-check" name="type" id="typeOut" value="out">
                                <label class="btn btn-outline-primary btn-sm fw-bold" for="typeOut">
                                    <i class="bi bi-dash-lg"></i> Sortie
                                </label>

                                <input type="radio" class="btn-check" name="type" id="typeLoss" value="loss">
                                <label class="btn btn-outline-danger btn-sm fw-bold" for="typeLoss">
                                    <i class="bi bi-x-lg"></i> Perte
                                </label>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label class="form-label fw-bold small">Quantité *</label>
                                <input type="number" name="quantity" step="0.01" min="0.01" value="1" class="form-control form-control-sm fw-bold" required>
                            </div>
                            <div class="col-7">
                                <label class="form-label fw-bold small">Service / Destination</label>
                                <select name="department" class="form-select form-select-sm">
                                    <option value="Économat">Économat Central</option>
                                    <option value="Bar Piscine">Bar & Piscine</option>
                                    <option value="Cuisine">Cuisine / Restaurant</option>
                                    <option value="Gouvernance">Gouvernance / Ménage</option>
                                    <option value="Réception">Réception</option>
                                    <option value="Maintenance">Maintenance</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Observation / N° Bon Livraison</label>
                            <input type="text" name="reason" class="form-control form-control-sm" placeholder="Ex: Livraison fournisseur, Dotation étage...">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                            <i class="bi bi-check2-circle me-1"></i> Enregistrer le Mouvement
                        </button>
                    </form>
                </div>
            </div>

            <!-- Historique rapide des 25 derniers mouvements -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-light py-2 px-3 border-bottom fw-bold small text-uppercase text-muted">
                    Derniers Mouvements
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush small">
                        @forelse($movements as $mv)
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <div>
                                    <div class="fw-bold text-dark">{{ $mv->item_name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        {{ \Carbon\Carbon::parse($mv->created_at)->format('d/m H:i') }} • {{ $mv->department }}
                                    </div>
                                </div>
                                @if($mv->type === 'in')
                                    <span class="badge bg-success-subtle text-success rounded-pill">+{{ $mv->quantity }} {{ $mv->item_unit }}</span>
                                @elseif($mv->type === 'out')
                                    <span class="badge bg-primary-subtle text-primary rounded-pill">-{{ $mv->quantity }} {{ $mv->item_unit }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill">-{{ $mv->quantity }} (Perte)</span>
                                @endif
                            </li>
                        @empty
                            <li class="list-group-item text-center py-3 text-muted">Aucun mouvement récent.</li>
                        @endforelse
                                        </ul>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : TABLEAU D'INVENTAIRE & ALERTES -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <form action="{{ route('stock.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-5">
                            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Toutes les catégories</option>
                                <option value="bar" {{ $category == 'bar' ? 'selected' : '' }}>Bar & Boissons</option>
                                <option value="cuisine" {{ $category == 'cuisine' ? 'selected' : '' }}>Cuisine & Denrées</option>
                                <option value="entretien" {{ $category == 'entretien' ? 'selected' : '' }}>Produits d'Entretien</option>
                                <option value="linge" {{ $category == 'linge' ? 'selected' : '' }}>Linge & Literie</option>
                                <option value="maintenance" {{ $category == 'maintenance' ? 'selected' : '' }}>Matériel & Maintenance</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStockCheck" {{ $onlyLow ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label small fw-bold text-danger" for="lowStockCheck">
                                    Afficher uniquement les alertes
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            @if($category || $onlyLow)
                                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Réinitialiser</a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4">Article</th>
                                    <th>Catégorie</th>
                                    <th class="text-center">Stock Actuel</th>
                                    <th class="text-center">Seuil Alerte</th>
                                    <th class="text-end">Valeur Stock</th>
                                    <th class="text-center pe-4">État</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    @php
                                        $isLow = $item->current_stock <= $item->alert_threshold;
                                        $valuation = $item->current_stock * $item->unit_cost;
                                    @endphp
                                    <tr style="{{ $isLow ? 'background-color: #fef2f2;' : '' }}">
                                        <td class="ps-4 fw-bold text-dark">{{ $item->name }}</td>
                                        <td><span class="badge bg-secondary-subtle text-dark text-uppercase">{{ $item->category }}</span></td>
                                        <td class="text-center fw-bold fs-6 {{ $isLow ? 'text-danger' : 'text-primary' }}">
                                            {{ $item->current_stock }} <small class="fw-normal text-muted">{{ $item->unit }}</small>
                                        </td>
                                        <td class="text-center text-muted">{{ $item->alert_threshold }} {{ $item->unit }}</td>
                                        <td class="text-end fw-semibold">{{ number_format($valuation, 0, ',', ' ') }} F</td>
                                        <td class="text-center pe-4">
                                            @if($isLow)
                                                <span class="badge bg-danger text-white rounded-pill px-3">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>À commander
                                                </span>
                                            @else
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            Aucun article enregistré dans l'économat. Cliquez sur <strong>Nouvel Article</strong> pour commencer.
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
</div>

<!-- Fenêtre Modale : Ajouter un Nouvel Article en Stock -->
<div class="modal fade" id="newItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('stock.items.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-box-seam text-warning me-2"></i>Ajouter un Article en Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catégorie *</label>
                    <select name="category" class="form-select" required>
                        <option value="bar">Bar & Boissons</option>
                        <option value="cuisine">Cuisine & Denrées</option>
                        <option value="entretien">Produits d'Entretien & Ménage</option>
                        <option value="linge">Linge & Literie</option>
                        <option value="maintenance">Matériel & Maintenance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">Désignation de l'article *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Pack Eau Minérale 1.5L, Eau de Javel 5L, Drap Blanc..." required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Unité de mesure *</label>
                        <input type="text" name="unit" value="unité" class="form-control" placeholder="bouteille, carton, litre, pièce..." required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Prix d'achat unitaire (F)</label>
                        <input type="number" name="unit_cost" value="0" min="0" step="50" class="form-control">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label fw-bold small text-primary">Stock Initial *</label>
                        <input type="number" name="current_stock" value="0" min="0" step="0.01" class="form-control fw-bold" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small text-danger">Seuil d'alerte minimum *</label>
                        <input type="number" name="alert_threshold" value="5" min="0" step="0.01" class="form-control fw-bold" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer l'Article</button>
            </div>
        </form>
    </div>
</div>
@endsection

