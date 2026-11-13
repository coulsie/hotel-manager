@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-door-open-fill text-primary me-2"></i>Gestion des Chambres
            </h3>
            <p class="text-muted small mb-0">Ajoutez de nouvelles chambres et modifiez leurs informations directement dans le tableau.</p>
        </div>
        <a href="{{ route('rooms.status') }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-grid-3x3-gap me-1"></i> Plan des chambres
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
        <!-- Formulaire d'ajout (Gauche) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Ajouter une Chambre</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.rooms.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Numéro de la chambre *</label>
                            <input type="text" name="number" value="{{ old('number') }}" class="form-control fw-bold" placeholder="Ex: 101, 205" maxlength="50" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type / Standing *</label>
                            <select name="type" class="form-select fw-semibold" required>
                                <option value="Standard">🛏️ Standard</option>
                                <option value="Double">👥 Double / Twin</option>
                                <option value="Deluxe">✨ Deluxe</option>
                                <option value="Suite Junior">👑 Suite Junior</option>
                                <option value="Suite VIP">💎 Suite VIP</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tarif par nuit (FCFA) *</label>
                            <div class="input-group">
                                <input type="number" name="price_per_night" value="{{ old('price_per_night') }}" class="form-control fw-bold text-success" placeholder="Ex: 35000" min="0" step="500" required>
                                <span class="input-group-text fw-bold">FCFA</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer la Chambre
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau des chambres avec colonne Action (Droite) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted">Chambres enregistrées ({{ isset($rooms) ? $rooms->count() : 0 }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">N°</th>
                                    <th>Type</th>
                                    <th class="text-end">Tarif / Nuit</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms ?? [] as $room)
                                    <!-- Ligne normale -->
                                    <tr id="room-view-{{ $room->id }}">
                                        <td class="ps-4 fw-bold text-primary">{{ $room->number }}</td>
                                        <td class="fw-semibold">{{ $room->type }}</td>
                                        <td class="text-end fw-bold text-success text-nowrap">{{ number_format($room->price_per_night, 0, ',', ' ') }} F</td>
                                        <td class="text-center">
                                            @if($room->status === 'available')
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Disponible</span>
                                            @elseif($room->status === 'occupied')
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Occupée</span>
                                            @elseif($room->status === 'dirty')
                                                <span class="badge bg-warning-subtle text-dark rounded-pill px-3">À nettoyer</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Maintenance</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <!-- Bouton Modifier -->
                                            <button type="button" onclick="toggleEditRoom({{ $room->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 me-1" title="Modifier">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>

                                            <!-- Bouton Supprimer -->
                                            <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer la chambre {{ $room->number }} ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>

                                    <!-- Ligne de modification (s'affiche au clic sur Modifier) -->
                                    <tr id="room-edit-{{ $room->id }}" class="d-none table-warning">
                                        <td colspan="5" class="p-3">
                                            <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" class="row g-2 align-items-center m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-2">
                                                    <input type="text" name="number" value="{{ $room->number }}" class="form-control form-control-sm fw-bold" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="type" class="form-select form-select-sm fw-semibold" required>
                                                        <option value="Standard" {{ $room->type === 'Standard' ? 'selected' : '' }}>Standard</option>
                                                        <option value="Double" {{ $room->type === 'Double' ? 'selected' : '' }}>Double / Twin</option>
                                                        <option value="Deluxe" {{ $room->type === 'Deluxe' ? 'selected' : '' }}>Deluxe</option>
                                                        <option value="Suite Junior" {{ $room->type === 'Suite Junior' ? 'selected' : '' }}>Suite Junior</option>
                                                        <option value="Suite VIP" {{ $room->type === 'Suite VIP' ? 'selected' : '' }}>Suite VIP</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" name="price_per_night" value="{{ intval($room->price_per_night) }}" class="form-control form-control-sm fw-bold text-success text-end" min="0" step="500" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="status" class="form-select form-select-sm fw-semibold">
                                                        <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Disponible</option>
                                                        <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupée</option>
                                                        <option value="dirty" {{ $room->status === 'dirty' ? 'selected' : '' }}>À nettoyer</option>
                                                        <option value="maintenance" {{ $room->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 text-end text-nowrap">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 me-1" title="Valider">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button type="button" onclick="toggleEditRoom({{ $room->id }})" class="btn btn-sm btn-secondary rounded-pill px-2" title="Annuler">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">Aucune chambre enregistrée.</td>
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
    function toggleEditRoom(id) {
        document.getElementById('room-view-' + id).classList.toggle('d-none');
        document.getElementById('room-edit-' + id).classList.toggle('d-none');
    }
</script>
@endsection
