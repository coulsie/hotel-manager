@extends('layouts.app')

@section('title', 'Attribution Carte RFID - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-card-list me-2"></i>Check-in : Attribution de Carte Magnétique</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Enregistrer un nouveau client</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('checkin.save') }}" method="POST">
                    @csrf

                    <!-- SECTION CLIENT -->
                    <div class="p-3 bg-light border rounded-3 mb-4">
                        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-person-bounding-box me-2"></i>Identité du Client</h6>

                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-bold small text-uppercase">Nom Complet / Raison Sociale</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" placeholder="Ex: COULIBALY YOUSSEF" required>
                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label for="customer_phone" class="form-label fw-bold small text-uppercase">N° Téléphone (Paiements)</label>
                                <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="Ex: 0700000000">
                            </div>
                            <div class="col-md-6">
                                <label for="id_card_number" class="form-label fw-bold small text-uppercase">N° Pièce d'identité</label>
                                <input type="text" class="form-control" id="id_card_number" name="id_card_number" placeholder="Ex: CNI / Passeport">
                            </div>
                        </div>
                    </div>

                    <!-- Sélection de la chambre -->
                    <div class="mb-3">
                        <label for="room_id" class="form-label fw-bold">Sélectionner la Chambre disponible</label>
                        <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                            <option value="" selected disabled>Choisir une chambre...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}">
                                    {{ $room->number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0, ',', ' ') }} F/nuit)
                                </option>
                            @endforeach
                        </select>
                        @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Code RFID de la carte avec Générateur Virtuel -->
                    <div class="mb-3">
                        <label for="card_uid" class="form-label fw-bold">Numéro Physique de la Carte (UID RFID)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control @error('card_uid') is-invalid @enderror" id="card_uid" name="card_uid" placeholder="Ex: RFID_88Z99K" required>
                            <button class="btn btn-outline-secondary" type="button" id="btn-simulate-card" onclick="simulateCardScan()">
                                <i class="bi bi-cpu-fill me-1"></i> Simuler un Badge
                            </button>
                        </div>
                        @error('card_uid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Durée du séjour -->
                    <div class="mb-3">
                        <label for="days" class="form-label fw-bold">Durée du séjour (Nuits)</label>
                        <input type="number" class="form-control @error('days') is-invalid @enderror" id="days" name="days" min="1" value="1" required>
                        @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- BLOC RÈGLEMENT & OPTION DIFFÉRÉE -->
                    <div class="p-3 bg-light border border-warning rounded-3 mb-4 shadow-sm">
                        <h6 class="fw-bold text-warning-emphasis mb-3"><i class="bi bi-wallet2 me-2"></i>Règlement des frais d'hébergement</h6>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Option de facturation</label>
                            <select class="form-select fw-bold border-primary" id="payment_type" name="payment_type" required>
                                <option value="full">💳 Paiement Total Immédiat (Garanti)</option>
                                <option value="partial">💵 Acompte / Paiement Partiel</option>
                                <option value="later">⏳ Payer plus tard / À la sortie (Note de Chambre)</option>
                            </select>
                        </div>

                        <div id="payment-details-wrapper">
                            <div class="row g-2 mb-3">
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold">Mode de Règlement</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="cash">💵 Espèces / Cash</option>
                                        <option value="wave">🧡 Wave</option>
                                        <option value="orange_money">🍊 Orange Money</option>
                                        <option value="mtn_momo">💛 MTN MoMo</option>
                                        <option value="card">💳 Carte Bancaire</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3" id="custom-amount-wrapper" style="display:none;">
                                <label class="form-label small fw-bold">Montant versé ce jour (F CFA)</label>
                                <input type="number" class="form-control fw-bold text-success" id="amount_paid" name="amount_paid" min="0" value="0">
                            </div>
                        </div>

                        <div class="alert alert-warning mb-2 py-2" id="alert-later-msg" style="display:none;">
                            <i class="bi bi-clock-history me-2"></i>Les nuitées seront imputées sur la note de chambre et payables au Checkout.
                        </div>

                        <!-- BLOC DES CALCULS EN DIRECTS (NET & RESTE À PAYER) -->
                        <div class="border-top pt-2 mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-muted small text-uppercase">Net à Payer (Total) :</span>
                                <span class="fw-bold text-dark fs-5" id="net-total-display">0 F CFA</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-muted small text-uppercase">Montant Encaissé :</span>
                                <span class="fw-bold text-success fs-5" id="amount-paid-display">0 F CFA</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top border-secondary-subtle pt-1">
                                <span class="fw-bold text-muted small text-uppercase" id="label-total">Reste à Payer :</span>
                                <span class="fs-4 fw-bold text-danger" id="total-display">0 F CFA</span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-check2-square me-2"></i>Valider et Enregistrer l'Arrivée</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Instructions à droite -->
    <div class="col-md-5">
        <div class="alert alert-info shadow-sm mb-4">
            <h5><i class="bi bi-info-circle-fill me-2"></i>Aperçu de la Facturation</h5>
            <p class="mb-0">Les données calculées ici seront retransmises à l'identique dans vos rapports d'encours et l'historique comptable général.</p>
        </div>
    </div>
</div>

<!-- LOGIQUE DE CALCUL DYNAMIQUE DU NET ET RESTE A PAYER -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomSelect = document.getElementById('room_id');
    const daysInput = document.getElementById('days');
    const paymentTypeSelect = document.getElementById('payment_type');
    const paymentDetailsWrapper = document.getElementById('payment-details-wrapper');
    const customAmountWrapper = document.getElementById('custom-amount-wrapper');
    const amountPaidInput = document.getElementById('amount_paid');
    const alertLaterMsg = document.getElementById('alert-later-msg');
    const labelTotal = document.getElementById('label-total');
    const netTotalDisplay = document.getElementById('net-total-display');
    const amountPaidDisplay = document.getElementById('amount-paid-display');
    const totalDisplay = document.getElementById('total-display');

    function calculateTotal(e) {
        const selectedOption = roomSelect ? roomSelect.options[roomSelect.selectedIndex] : null;
        const pricePerNight = selectedOption ? parseFloat(selectedOption.getAttribute('data-price')) : 0;
        const days = daysInput ? (parseInt(daysInput.value) || 1) : 1;
        const grandTotal = pricePerNight * days;

        let finalPaid = 0;
        let remaining = grandTotal;

        if (paymentTypeSelect && paymentTypeSelect.value === 'full') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'block';
            if (customAmountWrapper) customAmountWrapper.style.display = 'none';
            if (alertLaterMsg) alertLaterMsg.style.display = 'none';

            finalPaid = grandTotal;
            remaining = 0;
            if (amountPaidInput) amountPaidInput.value = grandTotal;
        }
        else if (paymentTypeSelect && paymentTypeSelect.value === 'partial') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'block';
            if (customAmountWrapper) customAmountWrapper.style.display = 'block';
            if (alertLaterMsg) alertLaterMsg.style.display = 'none';

            // Correction : On ne réinitialise à vide QUE si le changement vient du type de paiement
            if (e && e.type === 'change' && e.target === paymentTypeSelect) {
                if (amountPaidInput) amountPaidInput.value = '';
            }

            finalPaid = amountPaidInput ? (parseFloat(amountPaidInput.value) || 0) : 0;
            remaining = grandTotal - finalPaid;
            if (remaining < 0) remaining = 0;
        }
        else if (paymentTypeSelect && paymentTypeSelect.value === 'later') {
            if (paymentDetailsWrapper) paymentDetailsWrapper.style.display = 'none';
            if (alertLaterMsg) alertLaterMsg.style.display = 'block';

            finalPaid = 0;
            remaining = grandTotal;
            if (amountPaidInput) amountPaidInput.value = 0;
        }

        // Affichage des trois indicateurs au franc près
        const formatter = new Intl.NumberFormat('fr-FR');
        if (netTotalDisplay) netTotalDisplay.textContent = formatter.format(grandTotal) + ' F CFA';
        if (amountPaidDisplay) amountPaidDisplay.textContent = formatter.format(finalPaid) + ' F CFA';
        if (totalDisplay) {
            totalDisplay.textContent = formatter.format(remaining) + ' F CFA';
            totalDisplay.className = remaining > 0 ? "fs-4 fw-bold text-danger" : "fs-4 fw-bold text-success";
        }
        if (labelTotal) labelTotal.textContent = "Reste à Payer :";
    }

    if (roomSelect) roomSelect.addEventListener('change', calculateTotal);
    if (daysInput) daysInput.addEventListener('input', calculateTotal);
    if (paymentTypeSelect) paymentTypeSelect.addEventListener('change', calculateTotal);
    if (amountPaidInput) amountPaidInput.addEventListener('input', calculateTotal);

    // Premier calcul au chargement de la page
    calculateTotal();
});

function simulateCardScan() {
    const characters = '0123456789ABCDEF';
    let mockUid = 'RFID_';
    for (let i = 0; i < 8; i++) {
        mockUid += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    const cardInput = document.getElementById('card_uid');
    if (cardInput) cardInput.value = mockUid;
}
</script>

@endsection
