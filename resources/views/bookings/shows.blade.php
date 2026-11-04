@extends('layouts.app')

@section('title', 'Réservation Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-ticket-perforated me-2"></i>Réservation de Billets de Spectacle</h1>
</div>

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-printer me-2 text-info"></i>Émettre un billet</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bookings.shows.save') }}" method="POST">
                    @csrf

                    <!-- 1. Sélection de l'événement -->
                    <div class="mb-3">
                        <label for="event_id" class="form-label fw-bold text-dark small text-uppercase">Sélectionner l'Événement</label>
                        <select class="form-select fw-bold" id="event_id" name="event_id" required>
                            <option value="" selected disabled>Choisir un événement...</option>
                            @foreach($shows as $show)
                                <option value="{{ $show->id }}">
                                    {{ $show->title }} (Salle: {{ $show->venue->title ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Sélection du type de ticket (Chargé en JS) -->
                    <div class="mb-3">
                        <label for="event_ticket_id" class="form-label fw-bold text-dark small text-uppercase">Catégorie / Type de Ticket</label>
                        <select class="form-select fw-bold text-primary" id="event_ticket_id" name="event_ticket_id" required disabled>
                            <option value="" selected disabled>Veuillez d'abord choisir un événement...</option>
                        </select>
                    </div>

                    <!-- 3. Affichage Dynamique du Prix Réel et Places restantes -->
                    <div class="p-3 mb-4 rounded-3 bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-muted small text-uppercase">Montant à percevoir :</span>
                            <span id="final-price-display" class="fs-4 fw-black text-dark">0 F CFA</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-muted small text-uppercase">Places disponibles :</span>
                            <span id="available-seats-display" class="fw-bold text-secondary">--</span>
                        </div>
                        <input type="hidden" name="calculated_price" id="calculated_price" value="0">
                    </div>

                    <!-- 4. Bénéficiaire -->
                    <div class="mb-3">
                        <label for="beneficiary_type" class="form-label fw-bold text-dark small text-uppercase">Bénéficiaire</label>
                        <select class="form-select mb-2 text-secondary fw-semibold" id="beneficiary_type" name="beneficiary_type">
                            <option value="existing" selected>👤 Client enregistré de l'hôtel</option>
                            <option value="manual">✍️ Saisir manuellement (Passager / Externe)</option>
                        </select>
                        <div id="existing_user_container">
                            <select class="form-select" id="user_id" name="user_id">
                                <option value="" selected disabled>Choisir un client...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="manual_user_container" class="d-none">
                            <input type="text" class="form-control fw-bold" id="customer_name" name="customer_name" placeholder="Nom complet du bénéficiaire">
                        </div>
                    </div>

                    <!-- 5. Mode de Facturation -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-dark small text-uppercase">Règlement du Billet</label>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_direct" value="direct" checked>
                            <label class="form-check-label fw-semibold" for="charge_direct">Paiement Direct</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_room" value="room">
                            <label class="form-check-label text-danger fw-bold" for="charge_room">Sur la Chambre</label>
                        </div>
                    </div>

                    <div class="mb-4 d-none" id="show_room_container">
                        <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">Chambre à débiter</label>
                        <select class="form-select border-danger" id="room_id" name="room_id">
                            <option value="" selected disabled>Sélectionner la chambre...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="submit-btn" class="btn btn-danger w-100 rounded-pill fw-bold py-2.5 shadow-sm">
                        <i class="bi bi-printer me-2"></i>Confirmer et Émettre le Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Injection sécurisée des événements et de leurs tickets depuis Blade vers le JS
    const eventsData = @json($shows);

    const eventSelect = document.getElementById('event_id');
    const ticketSelect = document.getElementById('event_ticket_id');
    const priceDisplay = document.getElementById('final-price-display');
    const priceInput = document.getElementById('calculated_price');
    const seatsDisplay = document.getElementById('available-seats-display');
    const submitBtn = document.getElementById('submit-btn');

    const beneficiaryType = document.getElementById('beneficiary_type');
    const existingContainer = document.getElementById('existing_user_container');
    const manualContainer = document.getElementById('manual_user_container');
    const userIdSelect = document.getElementById('user_id');
    const customerNameInput = document.getElementById('customer_name');

    const chargeDirect = document.getElementById('charge_direct');
    const chargeRoom = document.getElementById('charge_room');
    const roomContainer = document.getElementById('show_room_container');
    const roomIdSelect = document.getElementById('room_id');

    // Mettre à jour les types de tickets disponibles au changement d'événement
    eventSelect.addEventListener('change', function() {
        const eventId = parseInt(this.value);
        const selectedEvent = eventsData.find(e => e.id === eventId);

        ticketSelect.innerHTML = '<option value="" selected disabled>Choisir une catégorie...</option>';
        priceDisplay.textContent = "0 F CFA";
        priceInput.value = 0;
        seatsDisplay.textContent = "--";

        if (selectedEvent && selectedEvent.tickets && selectedEvent.tickets.length > 0) {
            selectedEvent.tickets.forEach(ticket => {
                const option = document.createElement('option');
                option.value = ticket.id;
                option.setAttribute('data-price', ticket.price);
                option.setAttribute('data-seats', ticket.available_seats);

                // Capitalisation du nom de la catégorie (ex: vip -> VIP)
                const typeLabel = ticket.ticket_type.toUpperCase();
                option.textContent = `${typeLabel} — ${new Intl.NumberFormat('fr-FR').format(ticket.price)} F`;

                if(ticket.available_seats <= 0) {
                    option.disabled = true;
                    option.textContent += ' (COMPLET)';
                }
                ticketSelect.appendChild(option);
            });
            ticketSelect.disabled = false;
        } else {
            ticketSelect.innerHTML = '<option value="" selected disabled>Aucun ticket disponible pour cet événement</option>';
            ticketSelect.disabled = true;
        }
    });

    // Mettre à jour le prix et les places au choix de la catégorie
 // Mettre à jour le prix et les places au choix de la catégorie
    ticketSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption || selectedOption.value === "") return;

        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const seats = parseInt(selectedOption.getAttribute('data-seats')) || 0;

        priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(price) + " F CFA";
        priceInput.value = price;
        seatsDisplay.textContent = seats + " places restantes";

        if (seats <= 0) {
            if (submitBtn) submitBtn.disabled = true;
            seatsDisplay.classList.add('text-danger');
        } else {
            if (submitBtn) submitBtn.disabled = false;
            seatsDisplay.classList.remove('text-danger');
        }
    });

    // Commutation bénéficiaire (Sélection client vs Saisie manuelle)
    if (beneficiaryType) {
        beneficiaryType.addEventListener('change', function() {
            if (this.value === 'existing') {
                if (existingContainer) existingContainer.classList.remove('d-none');
                if (manualContainer) manualContainer.classList.add('d-none');
                if (userIdSelect) userIdSelect.required = true;
                if (customerNameInput) {
                    customerNameInput.required = false;
                    customerNameInput.value = '';
                }
            } else {
                if (existingContainer) existingContainer.classList.add('d-none');
                if (manualContainer) manualContainer.classList.remove('d-none');
                if (userIdSelect) {
                    userIdSelect.required = false;
                    userIdSelect.value = '';
                }
                if (customerNameInput) customerNameInput.required = true;
            }
        });
    }

    // Gestion affichage Facturation Chambre
    function toggleRoomField() {
        if (!chargeRoom || !roomContainer || !roomIdSelect) return;

        if (chargeRoom.checked) {
            roomContainer.classList.remove('d-none');
            roomIdSelect.required = true;
        } else {
            roomContainer.classList.add('d-none');
            roomIdSelect.required = false;
            roomIdSelect.value = '';
        }
    }

    if (chargeDirect) chargeDirect.addEventListener('change', toggleRoomField);
    if (chargeRoom) chargeRoom.addEventListener('change', toggleRoomField);

    // Initialisation par défaut au chargement
    if (userIdSelect) userIdSelect.required = true;
    toggleRoomField();
});
</script>

@endsection
