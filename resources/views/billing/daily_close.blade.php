@extends('layouts.app')

@section('title', 'Clôture de Caisse (Rapport Z) - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-3 mb-4 border-bottom d-print-none gap-3">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-safe2-fill text-success me-2"></i>Clôture de Caisse Journalière (Rapport Z)
        </h1>
        <p class="text-muted small mb-0 mt-1">Arrêté des recettes par mode d'encaissement et par pôle d'activité</p>
    </div>
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('billing.daily.close') }}" class="d-flex gap-2">
            <input type="date" name="date" class="form-control fw-bold" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()">
        </form>
        <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-printer-fill text-warning me-1"></i> Imprimer le Rapport Z
        </button>
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
                        <td>💵 Espèces (Cash en tiroir)</td>
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
    </div>
</div>
@endsection
