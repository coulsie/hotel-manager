@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-door-open-fill text-primary me-2"></i>Gestion des Chambres
            </h3>
            <p class="text-muted small mb-0">Ajoutez de nouvelles chambres au parc hôtelier et consultez les tarifs en vigueur.</p>
        </div>
        <a href="{{ route('rooms.status') }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">
            <i class="bi bi-grid-3x3-gap me-1"></i> Plan des chambres
        </a>
    </div>

    <!-- Messages de notification -->
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
        <!-- Colonne gauche : Formulaire aligné sur votre méthode store() -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white py-3 px-4">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-plus-circle me-2"></i>Ajouter une Chambre</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.rooms.store') }}" method="POST">
                        @csrf

                        <!-- Numéro de chambre -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Numéro de la chambre *</label>
                            <input type="text"
                                   name="number"
                                   value="{{ old('number') }}"
                                   class="form-control fw-bold @error('number') is-invalid @enderror"
                                   placeholder="Ex: 101, 205, Suite A"
                                   maxlength="50"
                                   required>
                            @error('number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Type de chambre -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Type / Standing *</label>
                            <select name="type" class="form-select fw-semibold @error('type') is-invalid @enderror" required>
                                <option value="Standard" {{ old('type') == 'Standard' ? 'selected' : '' }}>🛏️ Standard</option>
                                <option value="Double" {{ old('type') == 'Double' ? 'selected' : '' }}>👥 Double / Twin</option>
                                <option value="Deluxe" {{ old('type') == 'Deluxe' ? 'selected' : '' }}>✨ Deluxe</option>
                                <option value="Suite Junior" {{ old('type') == 'Suite Junior' ? 'selected' : '' }}>👑 Suite Junior</option>
                                <option value="Suite VIP" {{ old('type') == 'Suite VIP' ? 'selected' : '' }}>💎 Suite VIP</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tarif par nuit -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Tarif par nuit (FCFA) *</label>
                            <div class="input-group">
                                <input type="number"
                                       name="price_per_night"
                                       value="{{ old('price_per_night') }}"
                                       class="form-control fw-bold text-success @error('price_per_night') is-invalid @enderror"
                                       placeholder="Ex: 35000"
                                       min="0"
                                       step="500"
                                       required>
                                <span class="input-group-text fw-bold">FCFA</span>
                            </div>
                            @error('price_per_night')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Enregistrer la Chambre
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Colonne droite : Liste des chambres existantes -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted">Chambres enregistrées ({{ isset($rooms) ? $rooms->count() : 0 }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">N° Chambre</th>
                                    <th>Type</th>
                                    <th class="text-end">Tarif / Nuit</th>
                                    <th class="text-center pe-4">Statut actuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms ?? [] as $room)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $room->number }}</td>
                                        <td class="fw-semibold">{{ $room->type }}</td>
                                        <td class="text-end fw-bold text-success">
                                            {{ number_format($room->price_per_night, 0, ',', ' ') }} F
                                        </td>
                                        <td class="text-center pe-4">
                                            @if($room->status === 'available')
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Disponible</span>
                                            @elseif($room->status === 'occupied')
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Occupée</span>
                                            @elseif($room->status === 'dirty')
                                                <span class="badge bg-warning-subtle text-dark rounded-pill px-3">À nettoyer</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ ucfirst($room->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">
                                            Aucune chambre enregistrée pour le moment.
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
@endsection
