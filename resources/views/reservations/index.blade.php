@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête Premium avec dégradé & indicateurs -->
    <div class="p-4 rounded-4 text-white shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3"
         style="background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 50%, #4f46e5 100%);">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-calendar2-check-fill text-warning me-2"></i>Réservations Futures & Fiche de Police
            </h3>
            <p class="text-white-50 small mb-0">Planifiez les séjours à l'avance, encaissez les arrhes et éditez les fiches de police officielles.</p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <a href="{{ route('reservations.rack') }}" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-grid-3x3-gap-fill me-1"></i> Ouvrir le Rack Hôtelier
            </a>
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm fs-6">
                Arrivées aujourd'hui : <strong>{{ $stats['arrivals_today'] }}</strong>
            </span>
            <span class="badge bg-white text-success px-3 py-2 rounded-pill shadow-sm fs-6">
                Arrhes en cours : <strong>{{ number_format($stats['deposits_total'], 0, ',', ' ') }} F</strong>
            </span>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 border-start border-4 border-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0 border-start border-4 border-danger">
            <ul class="mb-0 small fw-semibold">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <!-- Formulaire de réservation (Gauche) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-bookmark-plus-fill text-warning me-2"></i>Nouvelle Réservation</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf

                        <!-- Sélection client existant ou nouveau -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-primary">Client existant (Optionnel)</label>
                            <select name="customer_id" id="existingCustomer" class="form-select border-primary-subtle">
                                <option value="">-- Nouveau client (saisir ci-dessous) --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->full_name }} ({{ $cust->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="newCustomerFields" class="p-3 rounded-4 border border-primary-subtle mb-3" style="background-color: #f8fafc;">
                            <div class="mb-2">
                                <label class="form-label fw-bold small">Nom & Prénoms *</label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}" class="form-control form-control-sm" placeholder="Ex: Kouassi Jean-Marc">
                            </div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Téléphone *</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-sm" placeholder="07 00 00 00 00">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small">Nationalité</label>
                                    <input type="text" name="nationality" value="{{ old('nationality', 'Ivoirienne') }}" class="form-control form-control-sm">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-5">
                                    <label class="form-label fw-bold small">Type Pièce</label>
                                    <select name="id_type" class="form-select form-select-sm">
                                        <option value="CNI">CNI</option>
                                        <option value="Passeport">Passeport</option>
                                        <option value="Permis">Permis</option>
                                        <option value="Attestation">Attestation</option>
                                    </select>
                                </div>
                                <div class="col-7">
                                    <label class="form-label fw-bold small">N° Pièce d'identité</label>
                                    <input type="text" name="id_card_number" value="{{ old('id_card_number') }}" class="form-control form-control-sm" placeholder="CI00123456">
                                </div>
                            </div>
                        </div>

                        <!-- Chambre et Dates -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-primary">Chambre souhaitée *</label>
                            <select name="room_id" id="bookingRoomSelect" class="form-select fw-bold border-primary-subtle" required>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}"
                                            data-price="{{ $room->price_per_night }}"
                                            {{ old('room_id', request('room_id')) == $room->id ? 'selected' : '' }}>
                                        Ch. {{ $room->number }} - {{ $room->type }} ({{ number_format($room->price_per_night, 0, ',', ' ') }} F/nuit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $defaultCheckIn = old('check_in_date', request('check_in_date', date('Y-m-d')));
                            $defaultCheckOut = old('check_out_date', date('Y-m-d', strtotime($defaultCheckIn . ' +1 day')));
                        @endphp

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Arrivée *</label>
                                <input type="date" name="check_in_date" id="bookingCheckIn" value="{{ $defaultCheckIn }}" min="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Départ *</label>
                                <input type="date" name="check_out_date" id="bookingCheckOut" value="{{ $defaultCheckOut }}" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <!-- Estimation automatique du séjour -->
                        <div id="stayEstimateBox" class="p-2 px-3 rounded-3 mb-3 d-flex justify-content-between align-items-center small fw-bold"
                             style="background-color: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe;">
                            <span>Durée : <span id="estNights">1</span> nuit(s)</span>
                            <span>Total Séjour : <span id="estTotal">0</span> F CFA</span>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Provenance</label>
                                <input type="text" name="provenance" value="{{ old('provenance') }}" class="form-control form-control-sm" placeholder="Ex: Bouaké">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Destination</label>
                                <input type="text" name="destination" value="{{ old('destination', 'Abidjan') }}" class="form-control form-control-sm" placeholder="Ex: Abidjan">
                            </div>
                        </div>

                        <!-- Acompte & Mode de règlement -->
                        <div class="p-3 rounded-4 border border-success-subtle mb-3" style="background-color: #f0fdf4;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-success">Arrhes versées (F CFA)</label>
                                    <input type="number" name="deposit_paid" id="bookingDepositInput" value="{{ old('deposit_paid', 0) }}" min="0" step="500" class="form-control form-control-sm fw-bold border-success-subtle">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-success">Mode paiement</label>
                                    <select name="payment_method" class="form-select form-select-sm fw-bold border-success-subtle">
                                        <option value="cash">💵 Espèces</option>
                                        <option value="wave">🧡 Wave</option>
                                        <option value="orange_money">🍊 Orange Money</option>
                                        <option value="mtn_momo">💛 MTN MoMo</option>
                                        <option value="moov_money">🔵 Moov Money</option>
                                        <option value="card">💳 Carte / TPE</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Alerte dynamique de disponibilité et tableau des chambres libres -->
                        <div id="availabilityAlert" class="alert d-none" role="alert"></div>

                        <button type="submit" class="btn w-100 py-2 fw-bold text-white rounded-pill shadow-sm"
                                style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
                            <i class="bi bi-calendar-plus-fill me-1"></i> Confirmer la Réservation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des réservations (Droite) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <h6 class="fw-bold mb-0 text-uppercase">
                        <i class="bi bi-list-ul text-warning me-2"></i>Planning des Réservations
                    </h6>
                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ $reservations->total() }} dossier(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Client</th>
                                    <th class="py-3">Chambre</th>
                                    <th class="py-3">Séjour</th>
                                    <th class="text-end py-3">Total / Arrhes</th>
                                    <th class="text-center py-3">Statut</th>
                                    <th class="text-end pe-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $res)
                                    <!-- 1. LIGNE D'AFFICHAGE STANDARD -->
                                    <tr id="res-view-{{ $res->id }}">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $res->customer->full_name }}</div>
                                            <div class="small text-muted">{{ $res->customer->phone }} • {{ $res->customer->id_type }}: {{ $res->customer->id_card_number ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill px-3">Ch. {{ $res->room->number }}</span>
                                            <div class="small text-muted mt-1">{{ $res->room->type }}</div>
                                        </td>
                                        <td class="small">
                                            <div><strong>Du :</strong> {{ $res->check_in_date->format('d/m/Y') }}</div>
                                            <div><strong>Au :</strong> {{ $res->check_out_date->format('d/m/Y') }} <span class="badge bg-light text-dark border">{{ $res->nights }}j</span></div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <div class="fw-bold text-dark">{{ number_format($res->total_amount, 0, ',', ' ') }} F</div>
                                            <div class="small fw-semibold text-success">Arrhes : {{ number_format($res->deposit_paid, 0, ',', ' ') }} F</div>
                                        </td>
                                        <td class="text-center">
                                            @if($res->status === 'confirmed')
                                                <span class="badge bg-warning text-dark rounded-pill px-3">En attente</span>
                                            @elseif($res->status === 'checked_in')
                                                <span class="badge bg-success text-white rounded-pill px-3">En séjour</span>
                                            @elseif($res->status === 'completed' || $res->status === 'checked_out')
                                                <span class="badge bg-info-subtle text-info rounded-pill px-3">Terminée</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Annulée</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <!-- Bouton Fiche de Police -->
                                            <a href="{{ route('reservations.police', $res->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-2 py-1 me-1" title="Imprimer la Fiche de Police">
                                                <i class="bi bi-printer-fill"></i>
                                            </a>

                                            @if($res->status === 'confirmed')
                                                <!-- Bouton Check-in direct -->
                                                <form action="{{ route('reservations.checkin', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer l\'arrivée du client et occuper la chambre ?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-bold me-1" title="Convertir en Check-in">
                                                        <i class="bi bi-box-arrow-in-right me-1"></i> Arrivée
                                                    </button>
                                                </form>

                                                <!-- Bouton Modifier -->
                                                <button type="button" onclick="toggleEditRes({{ $res->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 me-1" title="Modifier la réservation">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            @endif

                                            @if($res->status !== 'checked_in')
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('reservations.destroy', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cette réservation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" title="Supprimer">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- 2. LIGNE D'ÉDITION EN LIGNE (MASQUÉE PAR DÉFAUT) -->
                                    <tr id="res-edit-{{ $res->id }}" class="d-none table-warning">
                                        <td colspan="6" class="p-3">
                                            <form action="{{ route('reservations.update', $res->id) }}" method="POST" class="row g-2 align-items-center m-0">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-3">
                                                    <select name="room_id" class="form-select form-select-sm fw-bold" required>
                                                        @foreach($rooms as $r)
                                                            <option value="{{ $r->id }}" {{ $res->room_id == $r->id ? 'selected' : '' }}>
                                                                Ch. {{ $r->number }} ({{ number_format($r->price_per_night, 0, ',', ' ') }} F)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" name="check_in_date" value="{{ $res->check_in_date->format('Y-m-d') }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="date" name="check_out_date" value="{{ $res->check_out_date->format('Y-m-d') }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="deposit_paid" value="{{ $res->deposit_paid }}" min="0" max="{{ $res->total_amount }}" step="500" class="form-control form-control-sm fw-bold" placeholder="Arrhes">
                                                </div>
                                                <div class="col-md-3 text-end text-nowrap">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                                        <i class="bi bi-check-lg"></i> Enregistrer
                                                    </button>
                                                    <button type="button" onclick="toggleEditRes({{ $res->id }})" class="btn btn-sm btn-secondary rounded-pill px-2 ms-1">
                                                        Annuler
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-25"></i>
                                            Aucune réservation enregistrée pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($reservations->hasPages())
                        <div class="p-3 border-top bg-light">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Masquer/afficher les champs nouveau client
    document.getElementById('existingCustomer').addEventListener('change', function () {
        const box = document.getElementById('newCustomerFields');
        box.style.display = this.value ? 'none' : 'block';
    });

    // 2. Basculer l'édition en ligne dans le tableau
    function toggleEditRes(id) {
        document.getElementById('res-view-' + id).classList.toggle('d-none');
        document.getElementById('res-edit-' + id).classList.toggle('d-none');
    }

    // 3. Vérification en temps réel des dates, du coût et de la disponibilité des chambres
    document.addEventListener('DOMContentLoaded', function () {
        const roomSelect    = document.querySelector('select[name="room_id"]');
        const checkInInput  = document.querySelector('input[name="check_in_date"]');
        const checkOutInput = document.querySelector('input[name="check_out_date"]');
        const depositInput  = document.getElementById('bookingDepositInput');
        const estNightsEl   = document.getElementById('estNights');
        const estTotalEl    = document.getElementById('estTotal');
        const alertBox      = document.getElementById('availabilityAlert');
        const submitBtn     = document.querySelector('form[action="{{ route('reservations.store') }}"] button[type="submit"]');

        const activeReservations = {{ \Illuminate\Support\Js::from(
            \App\Models\Reservation::whereIn('status', ['confirmed', 'checked_in'])
                ->get(['room_id', 'check_in_date', 'check_out_date'])
                ->map(fn($r) => [
                    'room_id' => $r->room_id,
                    'in'      => $r->check_in_date->format('Y-m-d'),
                    'out'     => $r->check_out_date->format('Y-m-d'),
                ])
        ) }};

        const allRooms = {{ \Illuminate\Support\Js::from(
            $rooms->map(fn($r) => [
                'id'     => $r->id,
                'number' => $r->number,
                'type'   => $r->type,
                'price'  => $r->price_per_night,
                'status' => $r->status,
            ])
        ) }};

        window.selectAvailableRoom = function(roomId) {
            roomSelect.value = roomId;
            validateDatesAndRoom();
        };

        function validateDatesAndRoom() {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;
            const selectedRoomId = parseInt(roomSelect.value);
            const selectedRoom = allRooms.find(r => r.id === selectedRoomId);

            alertBox.className = 'alert d-none';
            alertBox.innerHTML = '';
            submitBtn.disabled = false;

            if (checkIn) {
                const nextDay = new Date(checkIn);
                nextDay.setDate(nextDay.getDate() + 1);
                const minOut = nextDay.toISOString().split('T');
                checkOutInput.min = minOut;
                if (checkOut && checkOut <= checkIn) {
                    checkOutInput.value = minOut;
                }
            }

            const finalCheckOut = checkOutInput.value;

            if (checkIn && finalCheckOut && finalCheckOut <= checkIn) {
                if (estNightsEl) estNightsEl.textContent = '0';
                if (estTotalEl) estTotalEl.textContent = '0';
                alertBox.className = 'alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-3';
                alertBox.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-x-fill text-danger fs-5 me-2"></i>
                        <div class="small fw-semibold text-danger">
                            La date de départ doit être strictement supérieure à la date d'arrivée.
                        </div>
                    </div>
                `;
                submitBtn.disabled = true;
                return;
            }

            if (checkIn && finalCheckOut && selectedRoom) {
                const d1 = new Date(checkIn);
                const d2 = new Date(finalCheckOut);
                const nights = Math.max(1, Math.round((d2 - d1) / (1000 * 60 * 60 * 24)));
                const totalStay = nights * parseFloat(selectedRoom.price || 0);

                if (estNightsEl) estNightsEl.textContent = nights;
                if (estTotalEl) estTotalEl.textContent = new Intl.NumberFormat('fr-FR').format(totalStay);
                if (depositInput) depositInput.max = totalStay;
            }

            if (checkIn && finalCheckOut && selectedRoomId) {
                const busyRoomIds = activeReservations
                    .filter(res => res.in < finalCheckOut && res.out > checkIn)
                    .map(res => res.room_id);

                const isOccupiedToday = (selectedRoom && selectedRoom.status === 'occupied' && checkIn === "{{ date('Y-m-d') }}");
                const isRoomBusy = busyRoomIds.includes(selectedRoomId) || isOccupiedToday || (selectedRoom && selectedRoom.status === 'maintenance');

                if (isRoomBusy) {
                    submitBtn.disabled = true;
                    const freeRooms = allRooms.filter(r => {
                        if (r.status === 'maintenance') return false;
                        if (r.status === 'occupied' && checkIn === "{{ date('Y-m-d') }}") return false;
                        return !busyRoomIds.includes(r.id);
                    });

                    if (freeRooms.length === 0) {
                        alertBox.className = 'alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-3';
                        alertBox.innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-x-octagon-fill fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger">Hôtel complet sur cette période</h6>
                                    <p class="small mb-0 text-dark">Aucune chambre n'est disponible pour les dates sélectionnées.</p>
                                </div>
                            </div>
                        `;
                    } else {
                        const rowsHtml = freeRooms.map(r => {
                            const formattedPrice = new Intl.NumberFormat('fr-FR').format(r.price);
                            return `
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">Ch. ${r.number}</td>
                                    <td class="text-muted">${r.type}</td>
                                    <td class="text-end fw-semibold text-dark">${formattedPrice} F<span class="text-muted fw-normal">/nuit</span></td>
                                    <td class="text-end pe-2">
                                        <button type="button" onclick="selectAvailableRoom(${r.id})" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0" style="font-size: 0.75rem;">
                                            Choisir
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('');

                        alertBox.className = 'alert bg-danger-subtle border border-danger-subtle shadow-sm rounded-4 p-3 mb-3';
                        alertBox.innerHTML = `
                            <div class="d-flex align-items-start mb-2">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 me-2 mt-1"></i>
                                <div>
                                    <div class="fw-bold text-danger">Chambre ${selectedRoom.number} indisponible à ces dates</div>
                                    <div class="small text-dark">Voici les chambres libres sur cette période :</div>
                                </div>
                            </div>

                            <div class="bg-white rounded-3 border overflow-hidden mt-2 shadow-sm">
                                <div class="table-responsive" style="max-height: 180px;">
                                    <table class="table table-sm table-hover align-middle mb-0 small">
                                        <thead class="table-light text-uppercase" style="font-size: 0.7rem;">
                                            <tr>
                                                <th class="ps-3">Chambre</th>
                                                <th>Type</th>
                                                <th class="text-end">Tarif</th>
                                                <th class="text-end pe-3">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${rowsHtml}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    }
                }
            }
        }

        roomSelect.addEventListener('change', validateDatesAndRoom);
        checkInInput.addEventListener('change', validateDatesAndRoom);
        checkOutInput.addEventListener('change', validateDatesAndRoom);
        validateDatesAndRoom();
    });
</script>
@endsection
