@extends('layouts.app')

@section('title', 'Réservation Spectacle - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-ticket-perforated-fill text-danger me-2"></i>Billetterie : <span style="color: #7c3aed;">Réservation de Spectacles</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Émission instantanée de billets VIP, Standard ou Balcon et imputation sur chambre</p>
    </div>
    <span class="badge text-white px-3 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #db2777 100%);">
        <i class="bi bi-music-note-beamed text-warning me-1"></i> Guichet Spectacles
    </span>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs suivantes :</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- ===================================================================== -->
    <!-- COLONNE DE GAUCHE : FORMULAIRE D'ÉMISSION DE BILLET (7 COLONNES)      -->
    <!-- ===================================================================== -->
    <div class="col-lg-7">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- En-tête Dégradé Violet Royal / Magenta -->
            <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #6d28d9 0%, #db2777 100%);">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-printer-fill text-warning me-2"></i>Émettre un billet de concert
                </h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('bookings.shows.save') }}" method="POST">
                    @csrf

                    <!-- 1. SECTION ÉVÉNEMENT & CATÉGORIE (Thème Violet / Lavande) -->
                    <div class="p-4 rounded-4 mb-4 border" style="background-color: #faf5ff; border-color: #e9d5ff !important;">
                        <h6 class="fw-bold mb-3 text-uppercase small" style="color: #7e22ce; letter-spacing: 0.5px;">
                            <i class="bi bi-stars me-2 fs-5"></i>1. Choix du Spectacle & Catégorie
                        </h6>

                        <div class="mb-3">
                            <label for="event_id" class="form-label fw-bold text-dark small text-uppercase">Sélectionner l'Événement</label>
                            <select class="form-select fw-bold text-dark" id="event_id" name="event_id" style="border-color: #d8b4fe;" required>
                                <option value="" selected disabled>Choisir un concert ou événement...</option>
                                @foreach($shows as $show)
                                    <option value="{{ $show->id }}">
                                        🎤 {{ $show->title }} (Salle : {{ $show->venue->title ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="ticket_type" class="form-label fw-bold text-dark small text-uppercase">Catégorie / Type de Ticket</label>
                            <select class="form-select fw-bold text-primary" id="ticket_type" name="ticket_type" style="border-color: #d8b4fe;" required disabled>
                                <option value="" selected disabled>Veuillez d'abord choisir un événement...</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. AFFICHAGE DYNAMIQUE DU PRIX & PLACES (Encadré Émeraude / Cyan) -->
                    <div class="p-4 mb-4 rounded-4 border border-success-subtle shadow-sm" style="background: linear-gradient(135deg, #ecfdf5 0%, #f0f9ff 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-success-subtle">
                            <span class="fw-bold text-secondary small text-uppercase"><i class="bi bi-cash-coin text-success me-1"></i> Montant à percevoir :</span>
                            <span id="final-price-display" class="fs-3 fw-bold text-success">0 F CFA</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary small text-uppercase"><i class="bi bi-people-fill text-primary me-1"></i> Places disponibles :</span>
                            <span id="available-seats-display" class="badge bg-primary text-white rounded-pill px-3 py-1 fw-bold fs-6">--</span>
                        </div>
                        <!-- Champ masqué transmis à Laravel -->
                        <input type="hidden" name="calculated_price" id="calculated_price" value="0">
                    </div>

                    <!-- 3. BÉNÉFICIAIRE DU BILLET (Thème Bleu Clair) -->
                    <div class="p-4 rounded-4 mb-4 border border-primary-subtle" style="background-color: #eff6ff;">
                        <h6 class="fw-bold text-primary mb-3 text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="bi bi-person-badge-fill me-2 fs-5"></i>2. Bénéficiaire du Billet
                        </h6>

                        <select class="form-select mb-3 text-dark fw-bold border-primary-subtle" id="beneficiary_type" name="beneficiary_type">
                            <option value="existing" selected>👤 Client enregistré de l'hôtel</option>
                            <option value="manual">✍️ Saisir manuellement (Passager / Externe)</option>
                        </select>

                        <!-- Conteneur Client Hôtel (Visible par défaut) -->
                        <div id="existing_user_container">
                            <label for="user_id" class="form-label text-dark fw-semibold small">Sélectionner le client de l'hôtel</label>
                            <select class="form-select border-primary-subtle" id="user_id" name="user_id" required>
                                <option value="" selected disabled>Choisir un client...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Conteneur Saisie Manuelle (Caché par défaut) -->
                        <div id="manual_user_container" style="display: none;">
                            <label for="customer_name" class="form-label text-dark fw-semibold small">Nom complet du client externe</label>
                            <input type="text" class="form-control fw-bold border-primary-subtle" id="customer_name" name="customer_name" placeholder="Ex: M. Koffi Konan Albert">
                        </div>
                    </div>

                    <!-- 4. MODE DE FACTURATION (Cartes cliquables Ambre / Rubis) -->
                    <div class="p-4 rounded-4 mb-4 border border-warning" style="background-color: #fffbeb;">
                        <h6 class="fw-bold text-warning-emphasis mb-3 text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="bi bi-credit-card-2-front-fill text-warning me-2 fs-5"></i>3. Règlement du Billet
                        </h6>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="w-100 p-3 bg-white rounded-3 border border-success-subtle shadow-sm d-flex align-items-center gap-2" for="charge_direct" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="charge_type" id="charge_direct" value="direct" checked>
                                    <span class="fw-bold text-success small">💵 Paiement Direct</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="w-100 p-3 bg-white rounded-3 border border-danger-subtle shadow-sm d-flex align-items-center gap-2" for="charge_room" style="cursor: pointer;">
                                    <input class="form-check-input m-0" type="radio" name="charge_type" id="charge_room" value="room">
                                    <span class="fw-bold text-danger small">🚪 Sur la Chambre</span>
                                </label>
                            </div>
                        </div>

                        <div class="p-3 rounded-3 border border-danger d-none" id="show_room_container" style="background-color: #fef2f2;">
                            <label for="room_id" class="form-label fw-bold text-danger small text-uppercase">Chambre occupée à débiter</label>
                            <select class="form-select fw-bold border-danger-subtle" id="room_id" name="room_id">
                                <option value="" selected disabled>Sélectionner la chambre...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">🚪 Chambre {{ $room->number }} ({{ $room->currentGuest->name ?? 'Occupée' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" id="submit-btn" class="btn btn-lg w-100 rounded-pill fw-bold text-white shadow py-3 text-uppercase" style="background: linear-gradient(135deg, #e11d48 0%, #9333ea 100%); border: none; letter-spacing: 0.5px;">
                        <i class="bi bi-ticket-detailed-fill me-2"></i>Confirmer et Émettre le Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- COLONNE DE DROITE : SPECTACLES À L'AFFICHE & RÈGLES (5 COLONNES)      -->
    <!-- ===================================================================== -->
    <div class="col-lg-5">
        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
            <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e1b4b 0%, #4338ca 100%);">
                <span><i class="bi bi-calendar-event-fill text-warning me-2"></i>Concerts Programmés</span>
                <span class="badge bg-warning text-dark rounded-pill px-3 fw-bold">{{ $shows->count() }} à l'affiche</span>
            </div>
            <div class="card-body p-3">
                @if($shows->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($shows as $s)
                            <div class="list-group-item px-2 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $s->title }}</div>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $s->venue->title ?? 'Salle Principale' }}
                                        </small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 fw-bold">
                                        {{ \Carbon\Carbon::parse($s->performance_date)->format('d/m H\hi') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted small">
                        Aucun spectacle programmé prochainement.
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #be123c 0%, #e11d48 100%);">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase small mb-3"><i class="bi bi-shield-check text-warning me-2"></i>Fonctionnement Billetterie</h6>
                <ul class="small mb-0 ps-3 opacity-90" style="line-height: 1.7;">
                    <li>Le stock de places disponibles se décrémente automatiquement dès l'émission du billet.</li>
                    <li>En mode <strong>Paiement Direct</strong>, le reçu est immédiatement enregistré dans le Grand Livre des recettes.</li>
                    <li>En mode <strong>Sur la Chambre</strong>, le montant du billet s'ajoute à la note globale de la chambre.</li>
                </ul>
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
