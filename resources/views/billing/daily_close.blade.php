@extends('layouts.app')

@section('title', 'Clôture de Caisse (Rapport Z) - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-4 border-bottom d-print-none gap-3">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-safe2-fill text-success me-2"></i>Clôture de Caisse Journalière (Rapport Z)
        </h1>
        <p class="text-muted small mb-0 mt-1">Arrêté des recettes par mode d'encaissement, sorties de caisse et solde net en tiroir</p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('billing.daily.close') }}" class="d-flex gap-2">
            <input type="date" name="date" class="form-control fw-bold" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()">
        </form>
        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-printer-fill text-warning me-1"></i> Imprimer le Rapport Z
        </button>
     @php
    $occupiedCount = \App\Models\Room::where('status', 'occupied')->count();
    $totalRooms    = max(1, \App\Models\Room::count());
    $occRate       = round(($occupiedCount / $totalRooms) * 100);

    $waLines = [
        "📊 *RAPPORT Z — CLÔTURE DE CAISSE*",
        "🏨 *" . config('hotel.name') . "*",
        "📅 Date : *" . $date->format('d/m/Y') . "*",
        "🛏️ Occupation : *{$occupiedCount}/{$totalRooms} chambres ({$occRate}%)*",
        "",
        "💰 *RECETTES PAR PÔLE :*",
        "• Hébergement : " . number_format($byService['room_booking'] ?? 0, 0, ',', ' ') . " F",
        "• Restaurant & Bar : " . number_format($byService['restaurant'] ?? 0, 0, ',', ' ') . " F",
        "• Pressing : " . number_format($byService['pressing'] ?? 0, 0, ',', ' ') . " F",
        "• Lavage Auto : " . number_format($byService['car_wash'] ?? 0, 0, ',', ' ') . " F",
        "• Salles & Coworking : " . number_format($byService['conferences'] ?? 0, 0, ',', ' ') . " F",
        "• Billetterie : " . number_format($byService['show_ticket'] ?? 0, 0, ',', ' ') . " F",
        "",
        "💳 *ENCAISSEMENTS PAR MODE :*",
        "• Espèces : " . number_format($byMethod['cash'] ?? 0, 0, ',', ' ') . " F",
        "• Wave : " . number_format($byMethod['wave'] ?? 0, 0, ',', ' ') . " F",
        "• Orange Money : " . number_format($byMethod['orange_money'] ?? 0, 0, ',', ' ') . " F",
        "• MTN MoMo : " . number_format($byMethod['mtn_momo'] ?? 0, 0, ',', ' ') . " F",
        "• Moov Money : " . number_format($byMethod['moov_money'] ?? 0, 0, ',', ' ') . " F",
        "• Carte / TPE : " . number_format($byMethod['card'] ?? 0, 0, ',', ' ') . " F",
        "",
        "📈 *TOTAL BRUT ENCAISSÉ : " . number_format($grandTotal, 0, ',', ' ') . " F CFA*",
        "💸 Dépenses / Sorties : -" . number_format($totalExpenses, 0, ',', ' ') . " F",
        "💵 *ESPÈCES NETTES EN TIROIR : " . number_format($netCashInDrawer, 0, ',', ' ') . " F CFA*",
        "✅ *SOLDE NET DU JOUR : " . number_format($netDailyTotal, 0, ',', ' ') . " F CFA*",
    ];

    $rawWaMessage = implode("\n", $waLines);
    $managerPhone = preg_replace('/\D+/', '', (string) config('hotel.manager_whatsapp', '2250707584396'));
    $waWebDomain  = implode('.', ['web', 'whatsapp', 'com']);
    $whatsappUrl  = 'https://' . $waWebDomain . '/send?phone=' . $managerPhone . '&text=' . rawurlencode($rawWaMessage);

@endphp

<div class="d-inline-flex flex-wrap align-items-center gap-2 no-print">
    <button type="button"
            id="btnSendWaZReport"
            data-url="{{ $whatsappUrl }}"
            class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-whatsapp me-1 fs-5 align-middle"></i> Envoyer sur WhatsApp Web
    </button>

    <button type="button"
            id="btnCopyZReport"
            data-text="{{ $rawWaMessage }}"
            class="btn btn-outline-success rounded-pill px-3 fw-bold">
        <i class="bi bi-clipboard me-1"></i> Copier le texte
    </button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnWa   = document.getElementById('btnSendWaZReport');
    const btnCopy = document.getElementById('btnCopyZReport');

    if (btnWa) {
        btnWa.addEventListener('click', function () {
            const url = this.getAttribute('data-url');
            if (url) {
                window.open(url, '_blank', 'noopener,noreferrer');
            }
        });
    }

    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            const text = this.getAttribute('data-text') || '';
            navigator.clipboard.writeText(text).then(() => {
                const original = this.innerHTML;
                this.innerHTML = '<i class="bi bi-check2-all me-1"></i> Rapport Z copié !';
                setTimeout(() => { this.innerHTML = original; }, 2500);
            });
        });
    }
});
</script>




    </div>
</div>

<!-- En-tête officiel pour l'impression -->
<div class="p-4 rounded-4 border bg-white shadow-sm mb-4">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h4 class="fw-bold text-uppercase mb-0">HOTEL MANAGER RESORT</h4>
            <small class="text-muted">Feuille de Clôture de Caisse • Service Comptabilité</small>
        </div>
        <div class="text-end">
            <span class="badge bg-dark fs-6 px-3 py-2">Date : {{ $date->format('d/m/Y') }}</span>
            <div class="small text-muted mt-1">Édité par : {{ Auth::user()->name }} à {{ now()->format('H:i') }}</div>
        </div>
    </div>

    <!-- Total Général Encaissé -->
    <div class="p-4 rounded-4 text-white text-center mb-4" style="background: linear-gradient(135deg, #065f46 0%, #10b981 100%);">
        <span class="text-uppercase fw-bold small opacity-75 d-block">Recette Totale Encaissée du Jour</span>
        <h1 class="fw-bold mb-0 display-5">{{ number_format($grandTotal, 0, ',', ' ') }} F CFA</h1>
        <small>{{ $transactions->count() }} reçu(s) validé(s)</small>
    </div>

    <div class="row g-4">
        <!-- 1. Ventilation par Mode de Paiement -->
        <div class="col-md-6">
            <h6 class="fw-bold text-uppercase text-primary border-bottom pb-2 mb-3">
                <i class="bi bi-wallet2 me-2"></i>1. Détail par Mode d'Encaissement
            </h6>
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr class="table-success fw-bold">
                        <td>💵 Espèces (Cash brut encaissé)</td>
                        <td class="text-end">{{ number_format($byMethod['cash'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>🧡 Wave Mobile Money</td>
                        <td class="text-end fw-bold">{{ number_format($byMethod['wave'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>🍊 Orange Money</td>
                        <td class="text-end fw-bold">{{ number_format($byMethod['orange_money'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>💛 MTN MoMo</td>
                        <td class="text-end fw-bold">{{ number_format($byMethod['mtn_momo'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>🔵 Moov Money</td>
                        <td class="text-end fw-bold">{{ number_format($byMethod['moov_money'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>💳 Carte Bancaire / TPE</td>
                        <td class="text-end fw-bold">{{ number_format($byMethod['card'], 0, ',', ' ') }} F</td>
                    </tr>
                    @if(($byMethod['other'] ?? 0) > 0)
                        <tr>
                            <td>✨ Autres Modes de Règlement</td>
                            <td class="text-end fw-bold">{{ number_format($byMethod['other'], 0, ',', ' ') }} F</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-uppercase small text-primary">Total Encaissements :</td>
                        <td class="text-end text-success fs-6">{{ number_format($grandTotal, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 2. Ventilation par Service (Tous pôles inclus) -->
        <div class="col-md-6">
            <h6 class="fw-bold text-uppercase text-indigo border-bottom pb-2 mb-3">
                <i class="bi bi-pie-chart-fill me-2"></i>2. Détail par Pôle d'Activité
            </h6>
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr>
                        <td>🏨 Hébergement Hôtel</td>
                        <td class="text-end fw-bold">{{ number_format($byService['room_booking'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>🍽️ Restauration & Bar Piscine</td>
                        <td class="text-end fw-bold">{{ number_format($byService['restaurant'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>👔 Pressing & Blanchisserie</td>
                        <td class="text-end fw-bold text-info-emphasis">{{ number_format($byService['pressing'] ?? 0, 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>🚗 Lavage Auto</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($byService['car_wash'] ?? 0, 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td><span class="badge text-white me-2" style="background-color: #4f46e5;">🏢</span> Salles de Réunion, Séminaires & Coworking</td>
                        <td class="text-end fw-bold">{{ number_format($byService['conferences'] ?? 0, 0, ',', ' ') }} F</td>
                    </tr>

                    <tr>
                        <td>🎟️ Billetterie & Spectacles</td>
                        <td class="text-end fw-bold text-danger">{{ number_format($byService['show_ticket'], 0, ',', ' ') }} F</td>
                    </tr>
                    <tr>
                        <td>✨ Autres Prestations / Suppléments</td>
                        <td class="text-end fw-bold text-secondary">{{ number_format($byService['other'] ?? 0, 0, ',', ' ') }} F</td>
                    </tr>
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td class="text-uppercase small text-indigo">Total par Pôles :</td>
                        <td class="text-end text-success fs-6">{{ number_format($grandTotal, 0, ',', ' ') }} F</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- 3. Sorties de Caisse, Dépenses & Acomptes RH du Jour -->
    <div class="mt-4 pt-3 border-top">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-uppercase text-danger mb-0">
                <i class="bi bi-box-arrow-up-right me-2"></i>3. Sorties de Caisse, Dépenses & Acomptes Personnel
            </h6>
            <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-1">
                Total Sorties : - {{ number_format($totalExpenses ?? 0, 0, ',', ' ') }} F
            </span>
        </div>

        <table class="table table-bordered table-sm align-middle small mb-4">
            <thead class="table-light text-uppercase text-muted">
                <tr>
                    <th class="ps-3">N° Bon</th>
                    <th>Libellé / Bénéficiaire</th>
                    <th>Mode de sortie</th>
                    <th class="text-end pe-3">Montant Décaissé</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dailyExpenses ?? [] as $exp)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $exp->voucher_number }}</td>
                        <td>
                            {{ $exp->description }}
                            @if($exp->reference === 'RH-AVANCE')
                                <span class="badge bg-warning text-dark ms-1">Acompte RH</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $exp->payment_method }}</span></td>
                        <td class="text-end pe-3 fw-bold text-danger">- {{ number_format($exp->amount, 0, ',', ' ') }} F</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-3 text-muted">Aucune dépense ni acompte versé sur cette journée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Synthèse Solde Net Espèces en Tiroir-Caisse -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 rounded-4 border bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-uppercase fw-bold text-muted">Résultat Net Global du Jour</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Toutes recettes moins toutes sorties</div>
                    </div>
                    <div class="fs-5 fw-bold text-dark">{{ number_format($netDailyTotal ?? $grandTotal, 0, ',', ' ') }} F</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 rounded-4 text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                    <div>
                        <div class="small text-uppercase fw-bold text-warning">Solde Net Espèces en Tiroir</div>
                        <div class="text-white-50" style="font-size: 0.75rem;">Cash encaissé ({{ number_format($byMethod['cash'], 0, ',', ' ') }} F) - Sorties cash ({{ number_format($cashExpenses ?? 0, 0, ',', ' ') }} F)</div>
                    </div>
                    <div class="fs-4 fw-bold text-white">{{ number_format($netCashInDrawer ?? $byMethod['cash'], 0, ',', ' ') }} F</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone de signatures pour remise de caisse -->
    <div class="row mt-4 pt-3 border-top text-center small">
        <div class="col-6">
            <strong>Signature Caissier(ère)</strong>
            <div style="height: 60px;"></div>
        </div>
        <div class="col-6">
            <strong>Visa Direction / Contrôle</strong>
            <div style="height: 60px;"></div>
        </div>
    </div>
</div>
@endsection
