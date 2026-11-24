@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête Premium -->
    <div class="p-4 rounded-4 text-white shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3"
         style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-building-check text-warning me-2"></i>Salles de Réunion, Séminaires & Coworking
            </h3>
            <p class="text-white-50 small mb-0">Gestion des espaces professionnels, réservations par créneau horaire et facturation dédiée.</p>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <button type="button" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newSpaceModal">
                <i class="bi bi-plus-circle-fill me-1"></i> Nouvel Espace / Salle
            </button>
            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm fs-6">
                Espaces actifs : <strong>{{ $stats['spaces_count'] }}</strong>
            </span>
            <span class="badge bg-white text-info px-3 py-2 rounded-pill shadow-sm fs-6">
                Résa. du jour : <strong>{{ $stats['bookings_today'] }}</strong>
            </span>
            <span class="badge bg-white text-success px-3 py-2 rounded-pill shadow-sm fs-6">
                CA Mois : <strong>{{ number_format($stats['revenue_month'], 0, ',', ' ') }} F</strong>
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

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 border-start border-4 border-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
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

    <!-- 1. GRILLE DES SALLES & ESPACES DISPONIBLES -->
    <div class="row g-3 mb-4">
        @foreach($spaces as $sp)
            @php
                $badgeStyle = match($sp->category) {
                    'reunion'   => ['bg' => '#2563eb', 'label' => '🤝 Salle de Réunion'],
                    'seminaire' => ['bg' => '#7c3aed', 'label' => '🎤 Salle de Séminaire'],
                    'coworking' => ['bg' => '#059669', 'label' => '💻 Espace Coworking'],
                    default     => ['bg' => '#475569', 'label' => strtoupper($sp->category)],
                };
            @endphp
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge text-white rounded-pill px-3 py-1" style="background-color: {{ $badgeStyle['bg'] }};">
                                {{ $badgeStyle['label'] }}
                            </span>
                            <span class="badge bg-light text-dark border rounded-pill">
                                <i class="bi bi-people-fill text-primary me-1"></i> {{ $sp->capacity }} {{ $sp->category === 'coworking' ? 'postes' : 'places' }}
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark mt-2 mb-1">{{ $sp->name }}</h5>
                        <p class="text-muted small mb-3">{{ $sp->equipments ?? 'Équipements standards inclus' }}</p>

                        <div class="row g-2 text-center bg-light p-2 rounded-3 border small">
                            <div class="col-4 border-end">
                                <div class="text-muted" style="font-size: 0.7rem;">HEURE</div>
                                <div class="fw-bold text-dark">{{ number_format($sp->hourly_rate, 0, ',', ' ') }} F</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="text-muted" style="font-size: 0.7rem;">DEMI-JOURNÉE</div>
                                <div class="fw-bold text-primary">{{ number_format($sp->half_day_rate, 0, ',', ' ') }} F</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted" style="font-size: 0.7rem;">JOURNÉE</div>
                                <div class="fw-bold text-success">{{ number_format($sp->full_day_rate, 0, ',', ' ') }} F</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        <!-- 2. FORMULAIRE DE RÉSERVATION (GAUCHE) -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);">
                    <h5 class="card-title fw-bold mb-0"><i class="bi bi-calendar-plus-fill text-warning me-2"></i>Réserver un Espace</h5>
                </div>
                <div class="card-body p-4 bg-white">
                    <form action="{{ route('conferences.bookings.store') }}" method="POST">
                        @csrf

                        <!-- Choix de l'espace -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-primary">Espace / Salle *</label>
                            <select name="space_id" id="spaceSelect" class="form-select fw-bold border-primary-subtle" required>
                                @foreach($spaces as $sp)
                                    <option value="{{ $sp->id }}"
                                            data-category="{{ $sp->category }}"
                                            data-capacity="{{ $sp->capacity }}"
                                            data-hourly="{{ $sp->hourly_rate }}"
                                            data-half="{{ $sp->half_day_rate }}"
                                            data-full="{{ $sp->full_day_rate }}">
                                        {{ $sp->name }} ({{ ucfirst($sp->category) }} - {{ $sp->capacity }} pl.)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Type de client : Externe / Entreprise OU Chambre Hôtel -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-primary">Affectation Client *</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="client_type" id="typeExternal" value="external" checked>
                                <label class="btn btn-outline-primary btn-sm fw-bold" for="typeExternal">🏢 Entreprise / Externe</label>

                                <input type="radio" class="btn-check" name="client_type" id="typeRoom" value="room">
                                <label class="btn btn-outline-primary btn-sm fw-bold" for="typeRoom">🚪 Client en Chambre</label>
                            </div>
                        </div>

                        <div id="externalClientBox" class="p-3 rounded-3 bg-light border mb-3">
                            <div class="mb-2">
                                <label class="form-label fw-bold small">Société / Nom du Client *</label>
                                <input type="text" name="client_name" value="{{ old('client_name') }}" class="form-control form-control-sm" placeholder="Ex: Cabinet Conseil Ivoire / M. Koné">
                            </div>
                            <div>
                                <label class="form-label fw-bold small">Téléphone</label>
                                <input type="text" name="client_phone" value="{{ old('client_phone') }}" class="form-control form-control-sm" placeholder="07 00 00 00 00">
                            </div>
                        </div>

                        <div id="roomClientBox" class="p-3 rounded-3 bg-warning-subtle border border-warning mb-3 d-none">
                            <label class="form-label fw-bold small text-dark">Chambre occupée à débiter *</label>
                            <select name="room_id" class="form-select form-select-sm fw-bold">
                                <option value="">-- Sélectionner une chambre --</option>
                                @foreach($occupiedRooms as $r)
                                    <option value="{{ $r->id }}">Chambre {{ $r->number }} ({{ $r->type }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Le montant sera ajouté directement à la facture de la chambre.</small>
                        </div>

                        <!-- Date & Créneau Horaire -->
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <label class="form-label fw-bold small">Date de réservation *</label>
                                <input type="date" name="booking_date" id="spaceDate" value="{{ old('booking_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Heure Début *</label>
                                <input type="time" name="start_time" id="spaceStart" value="{{ old('start_time', '08:30') }}" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Heure Fin *</label>
                                <input type="time" name="end_time" id="spaceEnd" value="{{ old('end_time', '17:00') }}" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <label class="form-label fw-bold small">Formule Tarifaire *</label>
                                <select name="pricing_type" id="pricingType" class="form-select form-select-sm fw-bold">
                                    <option value="full_day">Journée complète</option>
                                    <option value="half_day">Demi-journée</option>
                                    <option value="hourly">À l'heure</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <label class="form-label fw-bold small" id="attendeesLabel">Participants *</label>
                                <input type="number" name="attendees" id="attendeesInput" value="{{ old('attendees', 1) }}" min="1" class="form-control form-control-sm fw-bold" required>
                            </div>
                        </div>

                        <div class="p-2 px-3 rounded-3 mb-3 d-flex justify-content-between align-items-center fw-bold"
                             style="background-color: #eef2ff; color: #1e3a8a; border: 1px solid #bfdbfe;">
                            <span class="small">Montant Total :</span>
                            <span class="fs-6"><span id="estimatedSpaceTotal">0</span> F CFA</span>
                            <input type="hidden" name="total_amount" id="spaceTotalInput" value="0">
                        </div>

                        <div id="paymentBox" class="p-3 rounded-4 border border-success-subtle mb-3" style="background-color: #f0fdf4;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-success">Montant Perçu (F)</label>
                                    <input type="number" name="amount_paid" id="spaceAmountPaid" value="{{ old('amount_paid', 0) }}" min="0" step="500" class="form-control form-control-sm fw-bold">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold small text-success">Mode</label>
                                    <select name="payment_method" class="form-select form-select-sm fw-bold">
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

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Notes / Besoins logistiques</label>
                            <input type="text" name="notes" value="{{ old('notes') }}" class="form-control form-control-sm" placeholder="Ex: Disposition en U, pause-café 10h...">
                        </div>

                        <button type="submit" class="btn w-100 py-2 fw-bold text-white rounded-pill shadow-sm"
                                style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%);">
                            <i class="bi bi-check2-circle me-1"></i> Valider la Réservation
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 3. PLANNING DES RÉSERVATIONS (DROITE) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <h6 class="fw-bold mb-0 text-uppercase">
                        <i class="bi bi-calendar-week text-warning me-2"></i>Planning des Réservations Pro & Coworking
                    </h6>
                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ $bookings->count() }} réservation(s)</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4 py-3">Date & Créneau</th>
                                    <th class="py-3">Espace</th>
                                    <th class="py-3">Client / Société</th>
                                    <th class="text-end py-3">Total / Payé</th>
                                    <th class="text-center py-3">Statut</th>
                                    <th class="text-end pe-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $bk)
                                    @php
                                        $remaining = max(0, (float)$bk->total_amount - (float)$bk->amount_paid);
                                    @endphp
                                    <tr>
                                        <td class="ps-4 small">
                                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($bk->booking_date)->format('d/m/Y') }}</div>
                                            <span class="badge bg-light text-primary border">
                                                {{ substr($bk->start_time, 0, 5) }} - {{ substr($bk->end_time, 0, 5) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $bk->space_name }}</div>
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill" style="font-size: 0.7rem;">
                                                {{ strtoupper($bk->space_category) }} • {{ $bk->attendees }} {{ $bk->space_category === 'coworking' ? 'poste(s)' : 'pers.' }}
                                            </span>
                                        </td>
                                        <td class="small">
                                            <div class="fw-bold text-dark">{{ $bk->client_name }}</div>
                                            @if($bk->client_phone)
                                                <div class="text-muted">{{ $bk->client_phone }}</div>
                                            @endif
                                            @if($bk->notes)
                                                <div class="text-primary fst-italic" style="font-size: 0.72rem;">{{ $bk->notes }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <div class="fw-bold text-dark">{{ number_format($bk->total_amount, 0, ',', ' ') }} F</div>
                                            <div class="small text-success fw-semibold">Payé : {{ number_format($bk->amount_paid, 0, ',', ' ') }} F</div>
                                        </td>
                                        <td class="text-center">
                                            @if($bk->status === 'cancelled')
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Annulée</span>
                                            @elseif($bk->payment_status === 'paid')
                                                <span class="badge bg-success text-white rounded-pill px-3">Soldée</span>
                                            @elseif($bk->room_id)
                                                <span class="badge bg-warning text-dark rounded-pill px-3">Note Ch. {{ $bk->room_number }}</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Reste {{ number_format($remaining, 0, ',', ' ') }} F</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4 text-nowrap">
                                            @if($bk->status !== 'cancelled' && $remaining > 0 && !$bk->room_id)
                                                <form action="{{ route('conferences.bookings.status', $bk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Encaisser le solde de {{ number_format($remaining, 0, ',', ' ') }} F CFA ?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="pay">
                                                    <input type="hidden" name="payment_method" value="cash">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold me-1">
                                                        <i class="bi bi-cash-coin me-1"></i> Solder
                                                    </button>
                                                </form>

                                            @endif

                                            @if($bk->status !== 'cancelled')
                                               <form action="{{ route('conferences.bookings.status', $bk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Annuler cette réservation et libérer le créneau ?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="action" value="cancel">
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Annuler">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>

                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-building-x fs-2 d-block mb-2 opacity-25"></i>
                                            Aucune réservation de salle ou de coworking enregistrée.
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

<!-- MODALE : AJOUTER UNE NOUVELLE SALLE / ESPACE COWORKING -->
<div class="modal fade" id="newSpaceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('conferences.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <h5 class="modal-title fw-bold"><i class="bi bi-building-add text-warning me-2"></i>Ajouter un Espace Pro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nom de la salle / espace *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ex: Salle Conseil Océan" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label fw-bold small">Type d'espace *</label>
                        <select name="category" class="form-select" required>
                            <option value="reunion">🤝 Salle de Réunion</option>
                            <option value="seminaire">🎤 Salle de Séminaire</option>
                            <option value="coworking">💻 Espace Coworking</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label fw-bold small">Capacité (pl./postes) *</label>
                        <input type="number" name="capacity" min="1" value="20" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label fw-bold small">Tarif / Heure *</label>
                        <input type="number" name="hourly_rate" min="0" step="500" value="15000" class="form-control" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-bold small">Demi-Journée *</label>
                        <input type="number" name="half_day_rate" min="0" step="1000" value="50000" class="form-control" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label fw-bold small">Journée *</label>
                        <input type="number" name="full_day_rate" min="0" step="1000" value="90000" class="form-control" required>
                    </div>
                </div>
                <div>
                    <label class="form-label fw-bold small">Équipements inclus</label>
                    <textarea name="equipments" rows="2" class="form-control" placeholder="Vidéoprojecteur, sonorisation, Wi-Fi fibre, climatisation..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer l'espace</button>
            </div>
        </form>
    </div>
</div>

@if(session('print_billing_id'))
<script>
    window.open("{{ route('billing.print', session('print_billing_id')) }}", "_blank");
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeExternal = document.getElementById('typeExternal');
    const typeRoom     = document.getElementById('typeRoom');
    const extBox       = document.getElementById('externalClientBox');
    const roomBox      = document.getElementById('roomClientBox');
    const payBox       = document.getElementById('paymentBox');

    function toggleClientType() {
        const isRoom = typeRoom.checked;
        extBox.classList.toggle('d-none', isRoom);
        roomBox.classList.toggle('d-none', !isRoom);
        payBox.classList.toggle('d-none', isRoom);
    }
    typeExternal.addEventListener('change', toggleClientType);
    typeRoom.addEventListener('change', toggleClientType);

    const spaceSelect    = document.getElementById('spaceSelect');
    const pricingSelect  = document.getElementById('pricingType');
    const startInput     = document.getElementById('spaceStart');
    const endInput       = document.getElementById('spaceEnd');
    const attendeesInput = document.getElementById('attendeesInput');
    const attendeesLabel = document.getElementById('attendeesLabel');
    const totalDisplay   = document.getElementById('estimatedSpaceTotal');
    const amountPaidInp  = document.getElementById('spaceAmountPaid');

        function computeSpaceEstimate() {
        const opt = spaceSelect.options[spaceSelect.selectedIndex];
        if (!opt) return;

        const cat    = opt.getAttribute('data-category');
        const hourly = parseFloat(opt.getAttribute('data-hourly') || 0);
        const half   = parseFloat(opt.getAttribute('data-half') || 0);
        const full   = parseFloat(opt.getAttribute('data-full') || 0);
        const qty    = Math.max(1, parseInt(attendeesInput.value || '1', 10));

        attendeesLabel.textContent = (cat === 'coworking') ? 'Postes *' : 'Participants *';

        let hours = 1;
        if (startInput.value && endInput.value && endInput.value > startInput.value) {
            const [h1, m1] = startInput.value.split(':').map(Number);
            const [h2, m2] = endInput.value.split(':').map(Number);
            hours = Math.max(1, Math.ceil(((h2 * 60 + m2) - (h1 * 60 + m1)) / 60));
        }

        let base = full;
        if (pricingSelect.value === 'hourly') base = hourly * hours;
        else if (pricingSelect.value === 'half_day') base = half;

        const total = (cat === 'coworking') ? (base * qty) : base;

        totalDisplay.textContent = new Intl.NumberFormat('fr-FR').format(total);
        const totalInput = document.getElementById('spaceTotalInput');
        if (totalInput) totalInput.value = total;
        if (amountPaidInp) amountPaidInp.max = total;
    }


    spaceSelect.addEventListener('change', computeSpaceEstimate);
    pricingSelect.addEventListener('change', computeSpaceEstimate);
    startInput.addEventListener('change', computeSpaceEstimate);
    endInput.addEventListener('change', computeSpaceEstimate);
    attendeesInput.addEventListener('input', computeSpaceEstimate);
    computeSpaceEstimate();
});
</script>
@endsection
