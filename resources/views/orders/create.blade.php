@extends('layouts.app')

@section('title', 'Nouvelle Commande - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-cart-plus-fill text-warning me-2"></i>Prise de Commande <span class="text-primary">(Restaurant & Bar Piscine)</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Saisie rapide en salle et imputation directe sur table ou note de chambre</p>
    </div>
    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 rounded-pill fw-bold">
        <i class="bi bi-fire me-1"></i> Liaison Cuisine Active
    </span>
</div>

<!-- Message de succès éclatant -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-bold">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>{{ session('success') }}
            </div>
            <!-- BOUTON REÇU : Apparaît uniquement après validation réussie -->
            @if(session('order_id'))
                <button type="button" class="btn btn-light text-success btn-sm rounded-pill fw-bold px-3 shadow-sm" onclick="printReceipt({{ session('order_id') }})">
                    <i class="bi bi-printer-fill me-1"></i> Imprimer le Reçu
                </button>
            @endif
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
        @foreach($errors->all() as $error)
            <div><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('orders.store') }}" method="POST">
    @csrf
    <div class="row g-4">
        <!-- ===================================================================== -->
        <!-- COLONNE DE GAUCHE : PARAMÈTRES DE SERVICE (DÉGRADÉ AMBRE / ORANGE)    -->
        <!-- ===================================================================== -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
                <div class="card-header text-white fw-bold py-3 px-4" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-geo-alt-fill me-2"></i>Informations de Service
                    </h5>
                </div>
                <div class="card-body p-4" style="background-color: #fffbeb;">
                    <!-- Sélection Table/Transat -->
                    <div class="mb-4">
                        <label for="restaurant_table_id" class="form-label fw-bold text-dark small text-uppercase">Emplacement (Table / Transat)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-warning border-warning-subtle"><i class="bi bi-shop"></i></span>
                            <select class="form-select fw-bold text-dark border-warning-subtle" id="restaurant_table_id" name="restaurant_table_id" required>
                                <option value="" selected disabled>Choisir l'emplacement...</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}">
                                        🍽️ {{ $table->number }} — [{{ strtoupper($table->location_type) }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Mode de règlement HORIZONTAL -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small text-uppercase d-block">Affectation & Règlement</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="w-100 p-3 bg-white rounded-3 border border-success-subtle shadow-sm d-flex align-items-center gap-2" for="pay_direct" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="payment_method" id="pay_direct" value="direct" checked onclick="toggleRoomSelect(false)">
                                    <span class="fw-bold text-success small">💵 Comptoir / Cash</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="w-100 p-3 bg-white rounded-3 border border-danger-subtle shadow-sm d-flex align-items-center gap-2" for="pay_room" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="payment_method" id="pay_room" value="room" onclick="toggleRoomSelect(true)">
                                    <span class="fw-bold text-danger small">🚪 Note Chambre</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Sélection de la chambre (Masquée par défaut, thème Rubis doux) -->
                    <div class="p-3 rounded-3 border border-danger mt-3 d-none" id="room_selection_container" style="background-color: #fef2f2;">
                        <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">
                            <i class="bi bi-door-closed-fill me-1"></i>Attribuer à la Chambre occupée
                        </label>
                        <select class="form-select fw-bold text-dark border-danger-subtle" id="room_id" name="room_id">
                            <option value="" selected disabled>Choisir la chambre du client...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">🚪 {{ $room->number }} (Occupant actuel)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow py-3 text-uppercase" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; letter-spacing: 0.5px;">
                <i class="bi bi-send-check-fill me-2"></i>Valider et Envoyer en Cuisine
            </button>
        </div>

        <!-- ===================================================================== -->
        <!-- COLONNE DE DROITE : CARTE DU MENU (DÉGRADÉ BLEU ROYAL / INDIGO)       -->
        <!-- ===================================================================== -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-journal-richtext text-warning me-2"></i>Articles disponibles au Menu
                    </h5>
                    <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ $menuItems->count() }} plat(s) / boisson(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="small text-uppercase" style="background-color: #eff6ff; color: #1e40af;">
                                <tr>
                                    <th class="ps-4 py-3">Article</th>
                                    <th class="py-3">Zone</th>
                                    <th class="text-end py-3">Prix Unitaire</th>
                                    <th class="text-center pe-4 py-3" style="width: 140px;">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menuItems as $index => $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $item->name }}</div>
                                            @if($item->description)
                                                <small class="text-muted d-block">{{ $item->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->location_type === 'restaurant')
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-3 py-1 fw-bold">
                                                    🍽️ RESTAURANT
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-1 fw-bold">
                                                    🍹 BAR / PISCINE
                                                </span>
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($item->price, 0, ',', ' ') }} F</td>
                                        <td class="pe-4">
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="number" class="form-control fw-bold text-center border-primary-subtle" name="items[{{ $index }}][quantity]" min="0" value="0">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleRoomSelect(show) {
        const container = document.getElementById('room_selection_container');
        const roomInput = document.getElementById('room_id');
        if (show) {
            container.classList.remove('d-none');
            roomInput.setAttribute('required', 'required');
        } else {
            container.classList.add('d-none');
            roomInput.removeAttribute('required');
            roomInput.value = "";
        }
    }

    // Fonction d'impression du reçu IoT / Caisse hôtelière
    function printReceipt(orderId) {
        window.open('/orders/' + orderId + '/receipt', '_blank');
    }
</script>
@endsection
