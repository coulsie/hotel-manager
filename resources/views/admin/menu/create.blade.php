@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-egg-fried text-warning me-2"></i>Carte & Menu Restauration</h3>
            <p class="text-muted small mb-0">Ajoutez ou modifiez les repas, prix et disponibilités du Restaurant et du Bar Piscine.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-cart-plus me-1"></i> Prise de commande
        </a>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Colonne gauche : Formulaire d'ajout -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-success text-white py-3 px-4">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Ajouter un Repas / Boisson</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.menu.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom du plat / boisson *</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Ex: Poulet Braisé, Coca-Cola 33cl" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Accompagnements ou détails (optionnel)">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Prix (FCFA) *</label>
                            <div class="input-group">
                                <input type="number" name="price" value="{{ old('price') }}" class="form-control fw-bold text-success" min="0" required>
                                <span class="input-group-text fw-bold">FCFA</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Point de vente *</label>
                            <select name="location_type" class="form-select fw-semibold" required>
                                <option value="restaurant" {{ old('location_type') == 'restaurant' ? 'selected' : '' }}>🍽️ Restaurant Principal</option>
                                <option value="bar_piscine" {{ old('location_type') == 'bar_piscine' ? 'selected' : '' }}>🏊 Bar Piscine</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Ajouter au Menu
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Liste avec édition en ligne -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted">Articles enregistrés ({{ isset($menuItems) ? $menuItems->count() : 0 }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">Plat / Boisson</th>
                                    <th>Point de vente</th>
                                    <th class="text-end">Prix</th>
                                    <th class="text-center">Disponibilité</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($menuItems ?? [] as $item)
                                    <!-- 1. Ligne d'affichage -->
                                    <tr id="menu-view-{{ $item->id }}">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                                            @if($item->description)
                                                <div class="small text-muted text-truncate" style="max-width: 200px;">{{ $item->description }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->location_type === 'bar_piscine')
                                                <span class="badge bg-info text-dark">🏊 Bar Piscine</span>
                                            @else
                                                <span class="badge bg-primary">🍽️ Restaurant</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-success text-nowrap">
                                            {{ number_format($item->price, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center">
                                            @if($item->is_available)
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Disponible</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Épuisé</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <!-- Bouton Modifier -->
                                            <button type="button" onclick="toggleEditMenu({{ $item->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 me-1" title="Modifier">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Bouton Supprimer -->
                                            <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article du menu ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>

                                    <!-- 2. Ligne d'édition en ligne -->
                                    <tr id="menu-edit-{{ $item->id }}" class="d-none table-warning">
                                        <td colspan="5" class="p-2 px-3">
                                            <form action="{{ route('admin.menu.update', $item->id) }}" method="POST" class="row g-2 align-items-center m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-3">
                                                    <input type="text" name="name" value="{{ $item->name }}" class="form-control form-control-sm fw-bold" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="location_type" class="form-select form-select-sm fw-semibold" required>
                                                        <option value="restaurant" {{ $item->location_type === 'restaurant' ? 'selected' : '' }}>🍽️ Restaurant</option>
                                                        <option value="bar_piscine" {{ $item->location_type === 'bar_piscine' ? 'selected' : '' }}>🏊 Bar Piscine</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="price" value="{{ intval($item->price) }}" class="form-control form-control-sm fw-bold text-success text-end" min="0" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="is_available" class="form-select form-select-sm fw-semibold">
                                                        <option value="1" {{ $item->is_available ? 'selected' : '' }}>🟢 Disponible</option>
                                                        <option value="0" {{ !$item->is_available ? 'selected' : '' }}>🔴 Épuisé</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 text-end text-nowrap">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill py-0 px-2 me-1" title="Enregistrer">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button type="button" onclick="toggleEditMenu({{ $item->id }})" class="btn btn-sm btn-secondary rounded-pill py-0 px-2" title="Annuler">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">
                                            Aucun plat ou boisson enregistré pour le moment.
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

<script>
    function toggleEditMenu(id) {
        document.getElementById('menu-view-' + id).classList.toggle('d-none');
        document.getElementById('menu-edit-' + id).classList.toggle('d-none');
    }
</script>
@endsection
