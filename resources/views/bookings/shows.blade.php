@extends('layouts.app')

@section('title', 'Réservation Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-ticket-perforated me-2"></i>Réservation de Billets de Spectacle</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-printer me-2 text-info"></i>Émettre un billet multi-tarifs</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bookings.shows.save') }}" method="POST">
                    @csrf

                    <!-- Sélection du Spectacle / Événement -->
                    <div class="mb-3">
                        <label for="event_id" class="form-label fw-bold text-dark small text-uppercase">Sélectionner le Spectacle</label>
                        <select class="form-select @error('event_id') is-invalid @enderror fw-bold" id="event_id" name="event_id" required>
                            <option value="" selected disabled>Choisir un événement...</option>
                            @foreach($shows as $show)
                                <option value="{{ $show->id }}" data-base-price="{{ $show->ticket_price }}">
                                    {{ $show->title }} (Salle: {{ $show->venue->title ?? 'N/A' }} — Base: {{ number_format($show->ticket_price, 0, ',', ' ') }} F)
                                </option>
                            @endforeach
                        </select>
                        @error('event_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Type de Ticket (Différenciation de prix dynamique) -->
                    <div class="mb-3">
                        <label for="ticket_type" class="form-label fw-bold text-dark small text-uppercase">Catégorie / Type de Ticket</label>
                        <select class="form-select fw-bold text-primary" id="ticket_type" name="ticket_type" required>
                            <option value="populaire" data-factor="1">🎟️ Ticket Populaire (Tarif Normal)</option>
                            <option value="honneurs" data-factor="2">🌟 Invité d'Honneur (Tarif x 2)</option>
                            <option value="vip" data-factor="3.5">💎 VIP / Privilège (Tarif x 3.5)</option>
                            <option value="gratuit" data-factor="0">🎁 Invitation / Gratuit (0 F)</option>
                        </select>
                    </div>

                    <!-- Affichage Dynamique du Prix Final Calculé -->
                    <div class="p-3 mb-4 rounded-3 bg-light border d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small text-uppercase">Montant à percevoir :</span>
                        <span id="final-price-display" class="fs-4 fw-black text-dark">0 F CFA</span>
                        <input type="hidden" name="calculated_price" id="calculated_price" value="0">
                    </div>

                    <!-- Client / Utilisateur -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-bold text-dark small text-uppercase">Bénéficiaire / Client</label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="" selected disabled>Choisir un client...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode de Facturation -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-dark small text-uppercase">Règlement du Billet</label>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_direct" value="direct" checked onclick="toggleShowRoom(false)">
                            <label class="form-check-label fw-semibold" for="charge_direct">Paiement Direct (Comptoir)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_room" value="room" onclick="toggleShowRoom(true)">
                            <label class="form-check-label text-danger fw-bold" for="charge_room">Facturer sur la Chambre</label>
                        </div>
                    </div>

                    <!-- Sélection Chambre si Facturation sur Chambre -->
                    <div class="mb-4 d-none" id="show_room_container">
                        <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">Chambre occupée à débiter</label>
                        <select class="form-select border-danger" id="room_id" name="room_id">
                            <option value="" selected disabled>Sélectionner la chambre...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-bold py-2.5 shadow-sm">
                        <i class="bi bi-printer me-2"></i>Confirmer et Émettre le Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CORRIGÉ ET TOTALEMENT REFERMÉ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventSelect = document.getElementById('event_id');
        const typeSelect = document.getElementById('ticket_type');
        const priceDisplay = document.getElementById('final-price-display');
        const priceInput = document.getElementById('calculated_price');

        function calculateFinalPrice() {
            const selectedOption = eventSelect.options[eventSelect.selectedIndex];
            const selectedType = typeSelect.options[typeSelect.selectedIndex];

            if (!selectedOption || selectedOption.value === "") {
                priceDisplay.textContent = "0 F CFA";
                priceInput.value = 0;
                return;
            }

            const basePrice = parseFloat(selectedOption.getAttribute('data-base-price')) || 0;
            const factor = parseFloat(selectedType.getAttribute('data-factor')) || 1;

            const finalPrice = basePrice * factor;

            priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(finalPrice) + " F CFA";
            priceInput.value = finalPrice;
        }

        eventSelect.addEventListener('change', calculateFinalPrice);
        typeSelect.addEventListener('change', calculateFinalPrice);
    });

    function toggleShowRoom(show) {
        const container = document.getElementById('show_room_container');
        const roomSelect = document.getElementById('room_id');
        if (show) {
            container.classList.remove('d-none');
            roomSelect.setAttribute('required', 'required');
        } else {
            container.classList.add('d-none');
            roomSelect.removeAttribute('required');
            roomSelect.value = "";
        }
    }
</script>
@endsection
