@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-calendar2-check-fill text-primary me-2"></i>Réservations Futures & Fiche de Police
            </h3>
            <p class="text-muted small mb-0">Planifiez les séjours à l'avance, gérez les acomptes et éditez les fiches de police.</p>
        </div>
        <div class="d-flex gap-2">
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                Arrivées aujourd'hui : <strong>{{ $stats['arrivals_today'] }}</strong>
            </span>
            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                Acomptes en cours : <strong>{{ number_format($stats['deposits_total'], 0, ',', ' ') }} F</strong>
            </span>
        </div>
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
        <!-- Formulaire de réservation (Gauche) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-bookmark-plus me-2"></i>Nouvelle Réservation</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf

                        <!-- Sélection client existant ou nouveau -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Client existant (Optionnel)</label>
                            <select name="customer_id" id="existingCustomer" class="form-select">
                                <option value="">-- Nouveau client (saisir ci-dessous) --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}">{{ $cust->full_name }} ({{ $cust->phone }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="newCustomerFields" class="p-3 bg-light rounded-3 border mb-3">
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
                            <label class="form-label fw-bold small text-uppercase text-muted">Chambre souhaitée *</label>
                            <select name="room_id" class="form-select fw-bold" required>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">
                                        Ch. {{ $room->number }} - {{ $room->type }} ({{ number_format($room->price_per_night, 0, ',', ' ') }} F/nuit)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Arrivée *</label>
                                <input type="date" name="check_in_date" value="{{ old('check_in_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Départ *</label>
                                <input type="date" name="check_out_date" value="{{ old('check_out_date', date('Y-m-d', strtotime('+1 day'))) }}" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Provenance</label>
                                <input type="text" name="provenance" value="{{ old('provenance') }}" class="form-control form-control-sm" placeholder="Ex: Bouaké">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Destination</label>
                                <input type="text" name="destination" value="{{ old('destination', 'Abidjan') }}" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-7">
                                <label class="form-label fw-bold small">Acompte versé (FCFA)</label>
                                <input type="number" name="deposit_paid" value="{{ old('deposit_paid', 0) }}" min="0" step="500" class="form-control fw-bold text-success">
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-bold small">Mode paiement</label>
                                <select name="payment_method" class="form-select">
                                    <option value="Espèces">Espèces</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Orange Money">Orange Money</option>
                                    <option value="MTN Money">MTN Money</option>
                                    <option value="Carte Bancaire">Carte Bancaire</option>
                                </select>
                            </div>
                        </div>


                        <!-- Alerte dynamique de disponibilité et de dates -->
                        <div id="availabilityAlert" class="alert d-none small fw-semibold rounded-3 mb-3" role="alert"></div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                            <i class="bi bi-calendar-plus me-1"></i> Confirmer la Réservation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des réservations (Droite) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase text-muted">Planning des Réservations</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="ps-4">Client</th>
                                    <th>Chambre</th>
                                    <th>Séjour</th>
                                    <th class="text-end">Total / Acompte</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-end pe-4">Actions</th>
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
                                            <span class="badge bg-primary">Ch. {{ $res->room->number }}</span>
                                            <div class="small text-muted">{{ $res->room->type }}</div>
                                        </td>
                                        <td class="small">
                                            <div><strong>Du :</strong> {{ $res->check_in_date->format('d/m/Y') }}</div>
                                            <div><strong>Au :</strong> {{ $res->check_out_date->format('d/m/Y') }} ({{ $res->nights }}j)</div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <div class="fw-bold text-dark">{{ number_format($res->total_amount, 0, ',', ' ') }} F</div>
                                            <div class="small text-success">Acompte : {{ number_format($res->deposit_paid, 0, ',', ' ') }} F</div>
                                        </td>
                                        <td class="text-center">
                                            @if($res->status === 'confirmed')
                                                <span class="badge bg-warning-subtle text-dark rounded-pill px-3">Confirmée</span>
                                            @elseif($res->status === 'checked_in')
                                                <span class="badge bg-success-subtle text-success rounded-pill px-3">En séjour</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Annulée</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            <!-- Bouton Fiche de Police -->
                                            <a href="{{ route('reservations.police', $res->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0 me-1" title="Imprimer la Fiche de Police">
                                                <i class="bi bi-printer"></i>
                                            </a>

                                            @if($res->status === 'confirmed')
                                                <!-- Bouton Check-in direct -->
                                                <form action="{{ route('reservations.checkin', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer l\'arrivée du client et occuper la chambre ?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-2 py-0 me-1" title="Convertir en Check-in">
                                                        <i class="bi bi-box-arrow-in-right"></i> Arrivée (Check-in)
                                                    </button>
                                                </form>

                                                <!-- Bouton Modifier -->
                                                <button type="button" onclick="toggleEditRes({{ $res->id }})" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-0 me-1" title="Modifier la réservation">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            @endif

                                            @if($res->status !== 'checked_in')
                                                <!-- Bouton Supprimer -->
                                                <form action="{{ route('reservations.destroy', $res->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer définitivement cette réservation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-0" title="Supprimer">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- 2. LIGNE D'ÉDITION EN LIGNE (MASQUÉE PAR DÉFAUT) -->
                                    <tr id="res-edit-{{ $res->id }}" class="d-none table-warning">
                                        <td colspan="6" class="p-2 px-3">
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
                                                <div class="col-md-3">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Acompte</span>
                                                        <input type="number" name="deposit_paid" value="{{ intval($res->deposit_paid) }}" min="0" step="500" class="form-control fw-bold text-success text-end">
                                                    </div>
                                                </div>
                                                <div class="col-md-2 text-end text-nowrap">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill py-0 px-2 me-1" title="Enregistrer">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                    <button type="button" onclick="toggleEditRes({{ $res->id }})" class="btn btn-sm btn-secondary rounded-pill py-0 px-2" title="Annuler">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted small">Aucune réservation enregistrée pour le moment.</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                @if($reservations->hasPages())
                    <div class="card-footer bg-white">
                        {{ $reservations->links() }}
                    </div>
                @endif
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

    // 3. Vérification en temps réel des dates et de la disponibilité des chambres
    document.addEventListener('DOMContentLoaded', function () {
        const roomSelect = document.querySelector('select[name="room_id"]');
        const checkInInput = document.querySelector('input[name="check_in_date"]');
        const checkOutInput = document.querySelector('input[name="check_out_date"]');
        const alertBox = document.getElementById('availabilityAlert');
        const submitBtn = document.querySelector('form[action="{{ route('reservations.store') }}"] button[type="submit"]');

        // Liste des réservations actives injectée depuis Laravel
        const activeReservations = {{ \Illuminate\Support\Js::from(
            \App\Models\Reservation::whereIn('status', ['confirmed', 'checked_in'])
                ->get(['room_id', 'check_in_date', 'check_out_date'])
                ->map(fn($r) => [
                    'room_id' => $r->room_id,
                    'in'      => $r->check_in_date->format('Y-m-d'),
                    'out'     => $r->check_out_date->format('Y-m-d'),
                ])
        ) }};

        // Liste complète des chambres avec leurs tarifs
        const allRooms = {{ \Illuminate\Support\Js::from(
            $rooms->map(fn($r) => [
                'id'     => $r->id,
                'number' => $r->number,
                'type'   => $r->type,
                'price'  => $r->price_per_night,
                'status' => $r->status,
            ])
        ) }};

        // Sélectionner directement une chambre libre depuis le tableau d'alerte
        window.selectAvailableRoom = function(roomId) {
            roomSelect.value = roomId;
            validateDatesAndRoom();
        };

        function validateDatesAndRoom() {
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;
            const selectedRoomId = parseInt(roomSelect.value);

            // Ajuster automatiquement le minimum de la date de départ (arrivée + 1 jour)
            if (checkIn) {
                const nextDay = new Date(checkIn);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.min = nextDay.toISOString().split('T')[0];
            }

            // Règle 1 : La date de départ doit être strictement supérieure à la date d'arrivée
            if (checkIn && checkOut && checkOut <= checkIn) {
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

            // Règle 2 : Vérifier la disponibilité de la chambre sur les dates choisies
            if (checkIn && checkOut && selectedRoomId) {
                const busyRoomIds = activeReservations
                    .filter(res => res.in < checkOut && res.out > checkIn)
                    .map(res => res.room_id);

                const selectedRoom = allRooms.find(r => r.id === selectedRoomId);
                const isRoomBusy = busyRoomIds.includes(selectedRoomId) || (selectedRoom && selectedRoom.status === 'maintenance');

                if (isRoomBusy) {
                    const freeRooms = allRooms.filter(r => !busyRoomIds.includes(r.id) && r.status !== 'maintenance');

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

                    submitBtn.disabled = true;
                    return;
                }
            }

            // Tout est valide
            alertBox.className = 'alert d-none';
            submitBtn.disabled = false;
        }

        checkInInput.addEventListener('change', validateDatesAndRoom);
        checkOutInput.addEventListener('change', validateDatesAndRoom);
        roomSelect.addEventListener('change', validateDatesAndRoom);
        validateDatesAndRoom();
    });
</script>


@endsection
