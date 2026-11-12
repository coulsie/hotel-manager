@extends('layouts.app')

@section('title', 'Attribution Carte RFID - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 text-dark fw-bold mb-0">
            <i class="bi bi-card-list text-primary me-2"></i>Check-in : <span class="text-primary">Attribution de Carte Magnétique</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Enregistrement des arrivées, encodage des badges RFID et encaissement initial</p>
    </div>
    <span class="badge text-white px-3 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
        <i class="bi bi-door-open-fill text-warning me-1"></i> Accueil & Réception
    </span>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{!! session('success') !!}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i><strong>{{ session('error') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- ===================================================================== -->
    <!-- COLONNE DE GAUCHE : FORMULAIRE D'ENREGISTREMENT (8 COLONNES)          -->
    <!-- ===================================================================== -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- En-tête Dégradé Bleu Royal -->
            <div class="card-header text-white py-3 px-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-person-plus-fill text-warning me-2"></i>Enregistrer un nouveau client
                </h5>
            </div>

            <div class="card-body p-4">
                <form action="{{ route('checkin.save') }}" method="POST">
                    @csrf

                    <!-- 1. SECTION CLIENT (Thème Indigo / Bleu Clair) -->
                    <div class="p-4 rounded-4 mb-4 border border-primary-subtle shadow-sm" style="background-color: #eff6ff;">
                        <h6 class="fw-bold text-primary mb-3 text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="bi bi-person-bounding-box me-2 fs-5"></i>1. Identité du Client
                        </h6>

                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-bold text-dark small text-uppercase">Nom Complet / Raison Sociale</label>
                            <input type="text" class="form-control fw-semibold border-primary-subtle @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" placeholder="Ex: COULIBALY YOUSSEF" required>
                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label fw-bold text-dark small text-uppercase">N° Téléphone (Paiements)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-primary border-primary-subtle"><i class="bi bi-telephone-fill"></i></span>
                                    <input type="text" class="form-control border-primary-subtle" id="customer_phone" name="customer_phone" placeholder="Ex: 0700000000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="id_card_number" class="form-label fw-bold text-dark small text-uppercase">N° Pièce d'identité</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white text-primary border-primary-subtle"><i class="bi bi-person-vcard-fill"></i></span>
                                    <input type="text" class="form-control border-primary-subtle" id="id_card_number" name="id_card_number" placeholder="Ex: CNI / Passeport">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SECTION CHAMBRE & BADGE RFID (Thème Cyan / Turquoise) -->
                    <div class="p-4 rounded-4 mb-4 border border-info-subtle shadow-sm" style="background-color: #ecfeff;">
                        <h6 class="fw-bold text-info-emphasis mb-3 text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="bi bi-key-fill text-info me-2 fs-5"></i>2. Attribution Chambre & Serrure RFID
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="room_id" class="form-label fw-bold text-dark small text-uppercase">Chambre disponible</label>
                                <select class="form-select fw-bold text-dark border-info-subtle @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                    <option value="" selected disabled>Choisir une chambre...</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}">
                                            🚪 {{ $room->number }} — {{ strtoupper($room->type) }} ({{ number_format($room->price_per_night, 0, ',', ' ') }} F/nuit)
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="days" class="form-label fw-bold text-dark small text-uppercase">Séjour (Nuits)</label>
                                <input type="number" class="form-control fw-bold text-center text-primary border-info-subtle @error('days') is-invalid @enderror" id="days" name="days" min="1" value="1" required>
                                @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div>
                            <label for="card_uid" class="form-label fw-bold text-dark small text-uppercase">Numéro Physique de la Carte (UID RFID)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-info border-info-subtle"><i class="bi bi-upc-scan"></i></span>
                                <input type="text" class="form-control fw-bold text-dark border-info-subtle @error('card_uid') is-invalid @enderror" id="card_uid" name="card_uid" placeholder="Ex: RFID_88Z99K" required>
                                <button class="btn text-white fw-bold px-3" type="button" id="btn-simulate-card" onclick="simulateCardScan()" style="background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);">
                                    <i class="bi bi-cpu-fill me-1"></i> Simuler un Badge
                                </button>
                            </div>
                            @error('card_uid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <!-- 3. BLOC RÈGLEMENT & OPTION DIFFÉRÉE (Thème Ambre & Émeraude) -->
                    <div class="p-4 rounded-4 mb-4 border border-warning shadow-sm" style="background-color: #fffbeb;">
                        <h6 class="fw-bold text-warning-emphasis mb-3 text-uppercase small" style="letter-spacing: 0.5px;">
                            <i class="bi bi-wallet2 text-warning me-2 fs-5"></i>3. Règlement des frais d'hébergement
                        </h6>

                        <div class="mb-3">
                            <label for="payment_type" class="form-label small fw-bold text-dark text-uppercase">Option de facturation</label>
                            <select class="form-select fw-bold border-warning text-dark" id="payment_type" name="payment_type" required>
                                <option value="full">💳 Paiement Total Immédiat (Garanti)</option>
                                <option value="partial">💵 Acompte / Paiement Partiel</option>
                                <option value="later">⏳ Payer plus tard / À la sortie (Note de Chambre)</option>
                            </select>
                        </div>

                        <div id="payment-details-wrapper">
                            <div class="row g-2 mb-3">
                                <div class="col-md-12">
                                    <label for="payment_method" class="form-label small fw-bold text-dark text-uppercase">Mode de Règlement</label>
                                    <select class="form-select fw-semibold border-warning-subtle" id="payment_method" name="payment_method">

                                        <option value="cash">💵 Espèces / Cash</option>
                                        <option value="wave">🧡 Wave</option>
                                        <option value="orange_money">🍊 Orange Money</option>
                                        <option value="mtn_momo">💛 MTN MoMo</option>
                                        <option value="card">💳 Carte Bancaire</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3" id="custom-amount-wrapper" style="display:none;">
                                <label for="amount_paid" class="form-label small fw-bold text-success text-uppercase">Montant versé ce jour (F CFA)</label>
                                <input type="number" class="form-control fw-bold text-success border-success fs-5" id="amount_paid" name="amount_paid" min="0" value="0">
                            </div>
                        </div>

                        <div class="alert alert-warning border-warning mb-3 py-2 rounded-3" id="alert-later-msg" style="display:none;">
                            <i class="bi bi-clock-history me-2 fw-bold"></i>Les nuitées seront imputées sur la note de chambre et payables au Checkout.
                        </div>

                        <div class="p-3 bg-white rounded-4 border border-success-subtle shadow-sm mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="fw-bold text-secondary small text-uppercase">Net à Payer (Total Séjour) :</span>
                                <span class="fw-bold text-success fs-4" id="net-total-display">0 F CFA</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-secondary small text-uppercase">Reste à Payer (Solde) :</span>
                                <span class="fw-bold text-danger fs-5" id="remaining-balance-display">0 F CFA</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-lg w-100 rounded-pill fw-bold text-white shadow py-3 text-uppercase" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; letter-spacing: 0.5px;">
                        <i class="bi bi-check-circle-fill me-2"></i>Valider l'arrivée et activer le badge
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- COLONNE DE DROITE : CHAMBRES PRÊTES & CONSIGNES (4 COLONNES)          -->
    <!-- ===================================================================== -->
    <div class="col-lg-4">
        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
            <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #065f46 0%, #059669 100%);">
                <span><i class="bi bi-door-open-fill text-warning me-2"></i>Chambres Prêtes</span>
                <span class="badge bg-white text-success rounded-pill px-3 fw-bold">{{ $rooms->count() }} libre(s)</span>
            </div>
            <div class="card-body p-3">
                @if($rooms->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($rooms as $r)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-2 border-bottom">
                                <div>
                                    <span class="fw-bold text-dark">🚪 {{ $r->number }}</span>
                                    <span class="badge bg-secondary-subtle text-dark ms-1" style="font-size: 0.7rem;">{{ strtoupper($r->type) }}</span>
                                </div>
                                <span class="fw-bold text-success small">{{ number_format($r->price_per_night, 0, ',', ' ') }} F</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-danger fw-bold small">
                        <i class="bi bi-exclamation-octagon fs-3 d-block mb-2"></i>
                        Hôtel complet ! Aucune chambre disponible.
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);">
            <div class="card-body p-4">
                <h6 class="fw-bold text-uppercase small mb-3"><i class="bi bi-info-circle-fill text-warning me-2"></i>Règles de Sécurité & Badges</h6>
                <ul class="small mb-0 ps-3 opacity-90" style="line-height: 1.7;">
                    <li>Le badge RFID s'active instantanément dès la validation du formulaire.</li>
                    <li>L'accès à la chambre expire automatiquement à <strong>12h00</strong> le jour prévu du départ.</li>
                    <li>En cas d'acompte ou de note différée, le solde reste consultable dans le module <strong>Encours & Factures</strong>.</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- SCRIPT JAVASCRIPT : CALCUL EN TEMPS RÉEL & GÉNÉRATEUR RFID -->
<script>
// 1. Générateur virtuel de badge RFID
function simulateCardScan() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let uid = 'RFID_';
    for (let i = 0; i < 6; i++) {
        uid += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    const cardInput = document.getElementById('card_uid');
    if (cardInput) {
        cardInput.value = uid;
        // Petit flash vert visuel pour confirmer le scan
        cardInput.classList.add('is-valid');
        setTimeout(() => cardInput.classList.remove('is-valid'), 1500);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const roomSelect = document.getElementById('room_id');
    const daysInput = document.getElementById('days');
    const paymentType = document.getElementById('payment_type');
    const paymentDetailsWrapper = document.getElementById('payment-details-wrapper');
    const customAmountWrapper = document.getElementById('custom-amount-wrapper');
    const amountPaidInput = document.getElementById('amount_paid');
    const alertLaterMsg = document.getElementById('alert-later-msg');

    const netTotalDisplay = document.getElementById('net-total-display');
    const remainingDisplay = document.getElementById('remaining-balance-display');

    // 2. Fonction globale de recalcul des montants
    function calculateTotals() {
        let pricePerNight = 0;
        if (roomSelect && roomSelect.selectedIndex > 0) {
            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            pricePerNight = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        }

        const days = parseInt(daysInput ? daysInput.value : 1) || 1;
        const netTotal = pricePerNight * days;
        const type = paymentType ? paymentType.value : 'full';

        let paid = 0;
        let remaining = netTotal;

        if (type === 'full') {
            paid = netTotal;
            remaining = 0;
            if (amountPaidInput) amountPaidInput.value = netTotal;
        } else if (type === 'partial') {
            paid = parseFloat(amountPaidInput ? amountPaidInput.value : 0) || 0;
            if (paid > netTotal) {
                paid = netTotal; // Empêche de saisir un acompte supérieur au total
                if (amountPaidInput) amountPaidInput.value = netTotal;
            }
            remaining = netTotal - paid;
        } else if (type === 'later') {
            paid = 0;
            remaining = netTotal;
            if (amountPaidInput) amountPaidInput.value = 0;
        }

        // Formatage en Francs CFA
        if (netTotalDisplay) {
            netTotalDisplay.textContent = new Intl.NumberFormat('fr-FR').format(netTotal) + ' F CFA';
        }
        if (remainingDisplay) {
            remainingDisplay.textContent = new Intl.NumberFormat('fr-FR').format(remaining) + ' F CFA';
        }
    }

    // 3. Commutation visuelle selon l'option de facturation
    function handlePaymentTypeChange() {
        if (!paymentType) return;
        const type = paymentType.value;

        if (type === 'full') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'block';
            if (customAmountWrapper) customAmountWrapper.style.display = 'none';
            if (alertLaterMsg) alertLaterMsg.style.display = 'none';
        } else if (type === 'partial') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'block';
            if (customAmountWrapper) customAmountWrapper.style.display = 'block';
            if (alertLaterMsg) alertLaterMsg.style.display = 'none';
            if (amountPaidInput) amountPaidInput.value = 0;
        } else if (type === 'later') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'none';
            if (customAmountWrapper) customAmountWrapper.style.display = 'none';
            if (alertLaterMsg) alertLaterMsg.style.display = 'block';
        }

        calculateTotals();
    }

    // 4. Écouteurs d'événements en direct
    if (roomSelect) roomSelect.addEventListener('change', calculateTotals);
    if (daysInput) daysInput.addEventListener('input', calculateTotals);
    if (paymentType) paymentType.addEventListener('change', handlePaymentTypeChange);
    if (amountPaidInput) amountPaidInput.addEventListener('input', calculateTotals);

    // Initialisation au chargement de la page
    handlePaymentTypeChange();
});
</script>
@endsection
