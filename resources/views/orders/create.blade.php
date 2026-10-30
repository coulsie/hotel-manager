@extends('layouts.app')

@section('title', 'Nouvelle Commande - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cart-plus me-2"></i>Prise de Commande (Restaurant & Bar Piscine)</h1>
</div>

<!-- Message de succès en vert -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
            <!-- BOUTON REÇU : Apparaît uniquement après validation réussie -->
            @if(session('order_id'))
                <button type="button" class="btn btn-light btn-sm fw-bold border" onclick="printReceipt({{ session('order_id') }})">
                    <i class="bi bi-printer-fill me-1"></i> Imprimer le Reçu
                </button>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('orders.store') }}" method="POST">
    @csrf
    <div class="row">
        <!-- Colonne de gauche : Paramètres de la commande -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">Informations de Service</h5>
                </div>
                <div class="card-body">
                    <!-- Sélection Table/Transat -->
                    <div class="mb-4">
                        <label for="restaurant_table_id" class="form-label fw-bold">Emplacement (Table / Transat)</label>
                        <select class="form-select" id="restaurant_table_id" name="restaurant_table_id" required>
                            <option value="" selected disabled>Choisir l'emplacement...</option>
                            @foreach($tables as $table)
                                <option value="{{ $table->id }}">
                                    {{ $table->number }} [{{ strtoupper($table->location_type) }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode de règlement HORIZONTAL -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block">Mode de paiement</label>
                        <div class="d-flex gap-3 align-items-center bg-light p-2 rounded border">
                            <div class="form-check m-0">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_direct" value="direct" checked onclick="toggleRoomSelect(false)">
                                <label class="form-check-label fw-medium" for="pay_direct">Règlement Direct (Cash/Mobile)</label>
                            </div>
                            <div class="form-check m-0">
                                <input class="form-check-input" type="radio" name="payment_method" id="pay_room" value="room" onclick="toggleRoomSelect(true)">
                                <label class="form-check-label fw-medium text-danger" for="pay_room">Facturer sur la Chambre</label>
                            </div>
                        </div>
                    </div>

                    <!-- Sélection de la chambre (Masquée par défaut) -->
                    <div class="mb-3 d-none" id="room_selection_container">
                        <label for="room_id" class="form-label fw-bold text-danger">Attribuer à la Chambre</label>
                        <select class="form-select" id="room_id" name="room_id">
                            <option value="" selected disabled>Choisir la chambre du client...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }} (Occupant actuel)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                <i class="bi bi-send-check me-2"></i>Valider et Envoyer en Cuisine
            </button>
        </div>

        <!-- Colonne de droite : Sélection des Articles du Menu -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Articles disponibles au Menu</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th>Zone</th>
                                    <th class="text-end">Prix Unitaire</th>
                                    <th class="text-center" style="width: 140px;">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($menuItems as $index => $item)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $item->name }}</div>
                                            <small class="text-muted">{{ $item->description }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $item->location_type === 'restaurant' ? 'info' : 'warning' }} text-dark">
                                                {{ strtoupper($item->location_type) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($item->price, 0, ',', ' ') }} F</td>
                                        <td>
                                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                            <input type="number" class="form-control text-center" name="items[{{ $index }}][quantity]" min="0" value="0">
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
        // Ouvre le reçu thermique formaté dans un nouvel onglet prêt à l'impression
        window.open('/orders/' + orderId + '/receipt', '_blank');
    }
</script>
@endsection
