@extends('layouts.app')

@section('title', 'Réservation Spectacle - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-ticket-perforated text-danger me-2"></i>Réservation de Billets de Spectacle</h1>
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
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-header bg-dark text-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-printer text-info me-2"></i>Émettre un billet de concert</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('bookings.shows.save') }}" method="POST">
                    @csrf

                    <!-- 1. Sélection de l'événement -->
                    <div class="mb-3">
                        <label for="event_id" class="form-label fw-bold text-dark small text-uppercase">Sélectionner l'Événement</label>
                        <select class="form-select fw-bold" id="event_id" name="event_id" required>
                            <option value="" selected disabled>Choisir un concert ou événement...</option>
                            @foreach($shows as $show)
                                <option value="{{ $show->id }}">
                                    {{ $show->title }} (Salle: {{ $show->venue->title ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Sélection du type de ticket (CORRIGÉ : name="ticket_type") -->
                    <div class="mb-3">
                        <label for="ticket_type" class="form-label fw-bold text-dark small text-uppercase">Catégorie / Type de Ticket</label>
                        <select class="form-select fw-bold text-primary" id="ticket_type" name="ticket_type" required disabled>
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
                        <!-- Champ masqué transmis à Laravel -->
                        <input type="hidden" name="calculated_price" id="calculated_price" value="0">
                    </div>

                    <!-- 4. Bénéficiaire -->
                    <!-- 4. Bénéficiaire du billet -->
                    <div class="mb-3">
                        <label for="beneficiary_type" class="form-label fw-bold text-dark small text-uppercase">Bénéficiaire du billet</label>
                        <select class="form-select mb-3 text-secondary fw-semibold" id="beneficiary_type" name="beneficiary_type">
                            <option value="existing" selected>👤 Client enregistré de l'hôtel</option>
                            <option value="manual">✍️ Saisir manuellement (Passager / Externe)</option>
                        </select>

                        <!-- Conteneur Client Hôtel (Visible par défaut) -->
                        <div id="existing_user_container">
                            <label for="user_id" class="form-label text-muted small">Sélectionner le client de l'hôtel</label>
                            <select class="form-select" id="user_id" name="user_id" required>
                                <option value="" selected disabled>Choisir un client...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Conteneur Saisie Manuelle (Caché par défaut via style="display:none;") -->
                        <div id="manual_user_container" style="display: none;">
                            <label for="customer_name" class="form-label text-muted small">Nom complet du client externe</label>
                            <input type="text" class="form-control fw-bold" id="customer_name" name="customer_name" placeholder="Ex: M. Koffi Konan Albert">
                        </div>
                    </div>


                    <!-- 5. Mode de Facturation -->
                    <div class="mb-4">
                        <label class="form-label fw-bold d-block text-dark small text-uppercase">Règlement du Billet</label>
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_direct" value="direct" checked>
                            <label class="form-check-label fw-semibold" for="charge_direct">Paiement Direct (Cash / Carte)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="charge_type" id="charge_room" value="room">
                            <label class="form-check-label text-danger fw-bold" for="charge_room">Facturer sur la Chambre</label>
                        </div>
                    </div>

                    <div class="mb-4 d-none" id="show_room_container">
                        <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">Chambre à débiter</label>
                        <select class="form-select border-danger" id="room_id" name="room_id">
                            <option value="" selected disabled>Sélectionner la chambre...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">🚪 Chambre {{ $room->number }} ({{ $room->currentGuest->name ?? 'Occupée' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="submit-btn" class="btn btn-danger w-100 rounded-pill fw-bold Regal py-2.5 shadow-sm">
                        <i class="bi bi-printer me-2"></i>Confirmer et Émettre le Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- LOGIQUE JAVASCRIPT RE-SYNCHRONISÉE ET STRUCTURELLEMENT CORRIGÉE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Récupération sécurisée du tableau d'événements injecté par Blade
    const eventsData = @json($shows);

    const eventSelect = document.getElementById('event_id');
    const ticketSelect = document.getElementById('ticket_type');
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

    // ÉCOUTEUR 1 : Changement de concert
    if (eventSelect) {
        eventSelect.addEventListener('change', function() {
            const eventId = parseInt(this.value);
            const selectedEvent = eventsData.find(e => e.id === eventId);

            // Réinitialisation des affichages
            if (ticketSelect) {
                ticketSelect.innerHTML = '<option value="" selected disabled>Choisir une catégorie...</option>';
            }
            if (priceDisplay) priceDisplay.textContent = "0 F CFA";
            if (priceInput) priceInput.value = 0;
            if (seatsDisplay) seatsDisplay.textContent = "--";

            // Vérification si l'événement contient bien sa relation 'tickets' alimentée
            if (selectedEvent && selectedEvent.tickets && selectedEvent.tickets.length > 0) {
                selectedEvent.tickets.forEach(ticket => {
                    const option = document.createElement('option');
                    option.value = ticket.ticket_type;
                    option.setAttribute('data-price', ticket.price);
                    option.setAttribute('data-seats', ticket.available_seats);

                    const label = ticket.ticket_type.toUpperCase();
                    option.textContent = label + " — " + new Intl.NumberFormat('fr-FR').format(ticket.price) + " F";

                    if (parseInt(ticket.available_seats) <= 0) {
                        option.disabled = true;
                        option.textContent += ' (COMPLET)';
                    }
                    if (ticketSelect) ticketSelect.appendChild(option);
                });
                if (ticketSelect) ticketSelect.disabled = false;
            } else {
                if (ticketSelect) {
                    ticketSelect.innerHTML = '<option value="" selected disabled>Aucun tarif disponible pour ce concert</option>';
                    ticketSelect.disabled = true;
                }
            }
        });
    }

    // ÉCOUTEUR 2 : Choix de la catégorie de tarif
    if (ticketSelect) {
        ticketSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption || selectedOption.value === "") return;

            const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const seats = parseInt(selectedOption.getAttribute('data-seats')) || 0;

            if (priceDisplay) priceDisplay.textContent = new Intl.NumberFormat('fr-FR').format(price) + " F CFA";
            if (priceInput) priceInput.value = price;
            if (seatsDisplay) seatsDisplay.textContent = seats + " places disponibles";

            if (seats <= 0) {
                if (submitBtn) submitBtn.disabled = true;
                if (seatsDisplay) seatsDisplay.classList.add('text-danger');
            } else {
                if (submitBtn) submitBtn.disabled = false;
                if (seatsDisplay) seatsDisplay.classList.remove('text-danger');
            }
        });
    }

    // ÉCOUTEUR 3 : Commutation bénéficiaire (Existant vs Manuel via style.display direct)
    if (beneficiaryType) {
        beneficiaryType.addEventListener('change', function() {
            if (this.value === 'existing') {
                if (existingContainer) existingContainer.style.display = 'block';
                if (manualContainer) manualContainer.style.display = 'none';

                if (userIdSelect) {
                    userIdSelect.required = true;
                    userIdSelect.disabled = false;
                }
                if (customerNameInput) {
                    customerNameInput.required = false;
                    customerNameInput.disabled = true;
                    customerNameInput.value = '';
                }
            } else {
                if (existingContainer) existingContainer.style.display = 'none';
                if (manualContainer) manualContainer.style.display = 'block';

                if (userIdSelect) {
                    userIdSelect.required = false;
                    userIdSelect.disabled = true;
                    userIdSelect.value = '';
                }
                if (customerNameInput) {
                    customerNameInput.required = true;
                    customerNameInput.disabled = false;
                }
            }
        });
    }

    // ÉCOUTEUR 4 : Gestion affichage Facturation Chambre
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

    // Contrainte par défaut pour le chargement initial
    if (userIdSelect) userIdSelect.required = true;
}); // <-- Une seule accolade de fermeture ici à la fin pour sceller le DOM !
</script>

@endsection
