@extends('layouts.app')

@section('title', 'Grand Livre des Recettes - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom gap-3">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-cash-coin text-success me-2"></i>Grand Livre des Recettes & Comptes Clients
        </h1>
        <p class="text-muted small mb-0 mt-1">Suivi consolidé des engagements chambres, extras restaurant et ventes directes</p>
    </div>

    <!-- Groupe des deux boutons alignés à droite -->
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('billing.payments.export', request()->query()) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Exporter Excel (CSV)
        </a>

        <a href="{{ route('billing.index') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-calculator me-1"></i> Retour aux Encours
        </a>
    </div>
</div>


@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $sumRoomCost    = collect($roomAccounts)->sum('room_cost');
    $sumRestoCost   = collect($roomAccounts)->sum('resto_cost');
    $sumEngagement  = collect($roomAccounts)->sum('total_engagement');
    $sumAlreadyPaid = collect($roomAccounts)->sum('total_paid');
    $sumRemaining   = collect($roomAccounts)->sum('remaining');
    $sumDirect      = $directTransactions->sum('amount_paid');
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE FINANCIÈRE ÉCLATANTES                           -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Total Encaissé Période -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Total Encaissé (Période)</span>
                    <i class="bi bi-safe2-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($periodTotal, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Engagements Chambres -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Engagements Chambres</span>
                    <i class="bi bi-building-fill-check fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($sumEngagement, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Reste à Percevoir -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Reste à Percevoir</span>
                    <i class="bi bi-exclamation-diamond-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($sumRemaining, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>

    <!-- Carte 4 : Ventes Directes Comptoir -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase fw-bold small opacity-75">Comptoir & Spectacles</span>
                    <i class="bi bi-ticket-perforated-fill fs-4 opacity-75"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ number_format($sumDirect, 0, ',', ' ') }} F</h3>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. BARRE DE FILTRES & RECHERCHE COLORÉE                               -->
<!-- ===================================================================== -->
<div class="card shadow-sm border-primary-subtle mb-4 rounded-4" style="background-color: #f0f7ff;">
    <div class="card-body p-3">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="period" class="form-label fw-bold small text-primary text-uppercase">Période fiscale :</label>
                <select class="form-select fw-semibold border-primary-subtle" name="period" id="period" onchange="this.form.submit()">
                    <option value="" {{ request('period') == '' ? 'selected' : '' }}>🔄 Toutes les transactions</option>
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>📅 Aujourd'hui (Journalier)</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>📊 Cette semaine (Hebdomadaire)</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>🗓️ Ce mois (Mensuel)</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="search" class="form-label fw-bold small text-primary text-uppercase">Rechercher un reçu, une chambre ou un mode :</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-primary border-primary-subtle"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-primary-subtle" name="search" id="search" placeholder="Ex: REC-2026..., Chambre 102, cash, wave..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill shadow-sm">
                    <i class="bi bi-filter me-1"></i> Filtrer
                </button>
                @if(request('period') || request('search'))
                    <a href="{{ url()->current() }}" class="btn btn-danger rounded-pill px-3" title="Réinitialiser">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 3. BLOC 1 : COMPTES CHAMBRES CONSOLIDÉS (DÉGRADÉ BLEU ROYAL)          -->
<!-- ===================================================================== -->
<div class="card shadow border-0 mb-5 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
        <span><i class="bi bi-building-check text-warning me-2 fs-5"></i>Comptes Clients Hébergement (Engagements & Règlements Groupés)</span>
        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">{{ count($roomAccounts) }} dossier(s)</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #eff6ff; color: #1e40af;">
                    <tr>
                        <th class="ps-4 py-3">Chambre / Séjour</th>
                        <th class="text-end py-3">Hébergement</th>
                        <th class="text-end py-3">Extras Resto</th>
                        <th class="text-end py-3">Engagement Total</th>
                        <th class="text-end py-3 text-success">Déjà Payé</th>
                        <th class="text-end py-3 text-danger">Reste à Payer</th>
                        <th class="text-center pe-4 py-3">Détails & Reçus</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomAccounts as $index => $account)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-primary fs-6">🚪 Chambre {{ $account['room']->number }}</div>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2" style="font-size: 0.72rem;">
                                    {{ strtoupper($account['room']->type) }} • {{ $account['days'] }} nuit(s)
                                </span>
                            </td>
                            <td class="text-end small fw-semibold text-dark">{{ number_format($account['room_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end small fw-bold text-warning-emphasis">{{ number_format($account['resto_cost'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold text-dark fs-6">{{ number_format($account['total_engagement'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold text-success fs-6">{{ number_format($account['total_paid'], 0, ',', ' ') }} F</td>
                            <td class="text-end fw-bold fs-6 {{ $account['remaining'] > 0 ? 'text-danger' : 'text-success' }}">


                                           {{ number_format($account['remaining'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-info text-white rounded-pill fw-bold px-3 shadow-sm" type="button" data-bs-toggle="collapse" data-bs-target="#details-room-{{ $index }}">
                                    <i class="bi bi-eye-fill me-1"></i> Détails ({{ $account['transactions']->count() }})
                                </button>
                                <a href="{{ route('billing.room', $account['room']->id) }}" class="btn btn-sm btn-dark rounded-pill px-3 ms-1 shadow-sm" title="Voir la facture globale">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- LIGNE DÉPLIABLE AVEC LE DÉTAIL DES COMMANDES RESTO ET DES REÇUS -->
                        <tr class="collapse" id="details-room-{{ $index }}" style="background-color: #f8fafc;">
                            <td colspan="7" class="p-4 border-bottom border-primary-subtle">
                                <div class="row g-3">
                                    <!-- Détail des reçus encaissés -->
                                    <div class="col-md-7">
                                        <div class="p-3 bg-white rounded-4 border border-success-subtle shadow-sm">
                                            <h6 class="fw-bold small text-uppercase text-success mb-2">
                                                <i class="bi bi-check2-circle me-1 fs-6"></i>Historique des Versements / Reçus
                                            </h6>
                                            <ul class="list-group list-group-flush">
                                                @foreach($account['transactions'] as $trx)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 small">
                                                        <div>
                                                            <span class="badge bg-primary text-white me-1">{{ $trx->receipt_number }}</span>
                                                            <span class="text-muted">{{ $trx->created_at->format('d/m/Y H:i') }}</span>
                                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1">{{ strtoupper($trx->payment_method) }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="fw-bold text-success fs-6">{{ number_format($trx->amount_paid, 0, ',', ' ') }} F</span>
                                                            <a href="{{ route('billing.print', $trx->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill py-0 px-2" title="Imprimer ce reçu">
                                                                <i class="bi bi-printer-fill"></i>
                                                            </a>
                                                            @role('Admin')
                                                            <form action="{{ route('billing.payments.destroy', $trx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce reçu ?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger rounded-pill py-0 px-2"><i class="bi bi-trash-fill"></i></button>
                                                            </form>
                                                            @endrole
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>

                                    <!-- Détail des consommations restaurant liées -->
                                    <!-- Détail complet des Extras liés (Resto, Pressing, Lavage Auto, Spectacles) -->
                                    <div class="col-md-5">
                                        <div class="p-3 bg-white rounded-4 border border-warning-subtle shadow-sm h-100">
                                            <h6 class="fw-bold small text-uppercase text-warning-emphasis mb-2">
                                                <i class="bi bi-stars text-warning me-1 fs-6"></i>Extras Liés (Resto, Pressing, Auto & Spectacles)
                                            </h6>

                                            @php
                                                $hasOrders   = isset($account['orders']) && $account['orders']->count() > 0;
                                                $hasServices = isset($account['extra_services']) && $account['extra_services']->count() > 0;
                                                $hasOthers   = isset($account['other_billings']) && $account['other_billings']->count() > 0;
                                            @endphp

                                            @if($hasOrders || $hasServices || $hasOthers)
                                                <ul class="list-group list-group-flush">
                                                    <!-- 1. Commandes Restaurant -->
                                                    @if($hasOrders)
                                                        @foreach($account['orders'] as $ord)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 small">
                                                                <div>
                                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning me-1">🍽️ RESTO</span>
                                                                    <span class="fw-semibold text-dark">Commande #{{ $ord->id }}</span>
                                                                    <small class="text-muted">({{ $ord->created_at->format('d/m H:i') }})</small>
                                                                </div>
                                                                <span class="fw-bold text-warning-emphasis">{{ number_format($ord->total_amount, 0, ',', ' ') }} F</span>
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                    <!-- 2. Prestations Pressing & Lavage Auto -->
                                                    @if($hasServices)
                                                        @foreach($account['extra_services'] as $srv)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 small">
                                                                <div>
                                                                    @if($srv->category === 'pressing')
                                                                        <span class="badge text-white me-1" style="background-color: #0891b2;">👔 PRESSING</span>
                                                                    @else
                                                                        <span class="badge text-white me-1" style="background-color: #4f46e5;">🚗 AUTO</span>
                                                                    @endif
                                                                    <span class="fw-semibold text-dark">{{ $srv->service_name }} (×{{ $srv->quantity }})</span>
                                                                </div>
                                                                <span class="fw-bold text-primary">{{ number_format($srv->total_amount, 0, ',', ' ') }} F</span>
                                                            </li>
                                                        @endforeach
                                                    @endif

                                                    <!-- 3. Billets de Spectacles & Autres -->
                                                    <!-- 3. Billets de Spectacles & Autres reçus liés à la chambre -->
                                                        @if($hasOthers)
                                                            @foreach($account['other_billings'] as $other)
                                                                @if(!in_array($other->service_type, ['pressing', 'car_wash']))
                                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-0 small">
                                                                        <div>
                                                                            @if($other->service_type === 'show_ticket')
                                                                                <span class="badge text-white me-1" style="background-color: #db2777;">🎟️ SPECTACLE</span>
                                                                            @else
                                                                                <span class="badge bg-secondary text-white me-1">✨ {{ strtoupper($other->service_type) }}</span>
                                                                            @endif
                                                                            <span class="fw-semibold text-dark">Reçu {{ $other->receipt_number }}</span>
                                                                        </div>
                                                                        <span class="fw-bold text-danger">{{ number_format($other->amount_paid, 0, ',', ' ') }} F</span>
                                                                    </li>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                </ul>
                                            @else
                                                <div class="text-muted small py-2">Aucun extra (restaurant, pressing, lavage ou spectacle) imputé à cette chambre.</div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">Aucun dossier chambre enregistré sur cette période.</td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- PIED DE TABLEAU AVEC TOTAUX GÉNÉRAUX -->
                @if(count($roomAccounts) > 0)
                    <tfoot style="background-color: #e2e8f0;" class="border-top border-primary border-2">
                        <tr class="fw-bold align-middle">
                            <td class="ps-4 py-3 text-uppercase small text-primary">
                                <i class="bi bi-calculator-fill me-1"></i> Totaux Hébergement :
                            </td>
                            <td class="text-end small text-dark">{{ number_format($sumRoomCost, 0, ',', ' ') }} F</td>
                            <td class="text-end small text-warning-emphasis">{{ number_format($sumRestoCost, 0, ',', ' ') }} F</td>
                            <td class="text-end text-dark fs-6">{{ number_format($sumEngagement, 0, ',', ' ') }} F</td>
                            <td class="text-end text-success fs-6">{{ number_format($sumAlreadyPaid, 0, ',', ' ') }} F</td>
                            <td class="text-end text-danger fs-6">{{ number_format($sumRemaining, 0, ',', ' ') }} F</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>




<!-- RUBRIQUE : RÉSERVATIONS & ARRHES -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header text-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
            <div>
                <div class="fw-bold d-flex align-items-center">
                    <i class="bi bi-building-check text-white me-2 fs-5"></i>
                    <span>Réservations & Arrhes (Engagements Futurs)</span>
                </div>
                <div class="small text-white-50 mb-0">
                    Suivi des acomptes encaissés d'avance et des soldes restants avant l'arrivée des clients.
                </div>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-white text-success px-3 py-2 rounded-pill shadow-sm">
                    Total Arrhes : <strong>{{ number_format(collect($reservationAccounts)->sum('deposit_paid'), 0, ',', ' ') }} F</strong>
                </span>
                <span class="badge bg-white text-danger px-3 py-2 rounded-pill shadow-sm">
                    Reste à encaisser : <strong>{{ number_format(collect($reservationAccounts)->sum('remaining'), 0, ',', ' ') }} F</strong>
                </span>
            </div>
        </div>


    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase text-muted">
                    <tr>
                        <th class="ps-4">Client</th>
                        <th>Chambre & Période</th>
                        <th class="text-end">Total Séjour</th>
                        <th class="text-end">Arrhes Reçues</th>
                        <th class="text-end">Reste à Payer</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end pe-4">Reçu Officiel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservationAccounts as $acc)
                        @php
                            $res = $acc['reservation'];
                            $firstReceipt = $acc['billings']->first();
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $res->customer->full_name }}</div>
                                <div class="small text-muted">{{ $res->customer->phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary">Ch. {{ $res->room->number }}</span>
                                <span class="small text-muted ms-1">({{ $res->nights }} nuit{{ $res->nights > 1 ? 's' : '' }})</span>
                                <div class="small text-muted mt-1">
                                    Du {{ $res->check_in_date->format('d/m/Y') }} au {{ $res->check_out_date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-dark">
                                {{ number_format($acc['total_amount'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-bold text-success">
                                {{ number_format($acc['deposit_paid'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-end fw-bold {{ $acc['remaining'] > 0 ? 'text-danger' : 'text-muted' }}">
                                {{ number_format($acc['remaining'], 0, ',', ' ') }} F
                            </td>
                            <td class="text-center">
                                @if($acc['remaining'] == 0)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Soldée</span>
                                @elseif($acc['deposit_paid'] > 0)
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3">Arrhes versées</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">Sans acompte</span>
                                @endif
                            </td>
                            <td class="text-end pe-4 text-nowrap">
                                @if($firstReceipt)
                                    <a href="{{ route('billing.print', $firstReceipt->id) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3" title="Imprimer le reçu d'arrhes">
                                        <i class="bi bi-printer me-1"></i> {{ $firstReceipt->receipt_number }}
                                    </a>
                                @else
                                    <span class="text-muted small">Aucun versement</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Aucune réservation en attente d'arrivée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- ===================================================================== -->
<!-- 4. BLOC 2 : VENTES DIRECTES REGROUPÉES PAR SERVICE                    -->
<!-- ===================================================================== -->
@php
    $serviceGroups = [
        'restaurant' => [
            'label' => '🍽️ Restauration & Bar Piscine',
            'badge' => 'bg-warning text-dark',
            'bg'    => '#fffbeb',
            'color' => '#92400e',
        ],
        'pressing' => [
            'label' => '👔 Pressing & Blanchisserie',
            'badge' => 'text-white',
            'style' => 'background-color: #0891b2;',
            'bg'    => '#ecfeff',
            'color' => '#155e75',
        ],
        'car_wash' => [
            'label' => '🚗 Lavage Auto',
            'badge' => 'text-white',
            'style' => 'background-color: #4f46e5;',
            'bg'    => '#eef2ff',
            'color' => '#3730a3',
        ],
        'show_ticket' => [
            'label' => '🎟️ Billetterie & Spectacles',
            'badge' => 'text-white',
            'style' => 'background-color: #db2777;',
            'bg'    => '#fdf2f8',
            'color' => '#9d174d',
        ],
    ];
    $groupedDirect = $directTransactions->groupBy('service_type');
@endphp

<div class="card shadow border-0 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #065f46 0%, #059669 100%);">
        <span><i class="bi bi-wallet2 text-warning me-2 fs-5"></i>Encaissements Directs Comptoir Regroupés par Service</span>
        <span class="badge bg-white text-success rounded-pill px-3 py-1 fw-bold">{{ $directTransactions->count() }} reçu(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #ecfdf5; color: #065f46;">
                    <tr>
                        <th class="ps-4 py-3">N° Reçu</th>
                        <th class="py-3">Date & Heure</th>
                        <th class="py-3">Service</th>
                        <th class="py-3">Origine</th>
                        <th class="py-3">Mode</th>
                        <th class="text-end py-3 text-success">Somme Perçue</th>
                        <th class="text-center pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceGroups as $typeKey => $meta)
                        @php $items = $groupedDirect->get($typeKey, collect()); @endphp
                        @if($items->count() > 0)
                            <!-- En-tête de groupe par service -->
                            <tr style="background-color: {{ $meta['bg'] }};">
                                <td colspan="5" class="ps-4 py-2 fw-bold text-uppercase small" style="color: {{ $meta['color'] }};">
                                    {{ $meta['label'] }} <span class="badge bg-white text-dark border ms-1">{{ $items->count() }} reçu(s)</span>
                                </td>
                                <td class="text-end fw-bold small" style="color: {{ $meta['color'] }};">
                                    Sous-total : {{ number_format($items->sum('amount_paid'), 0, ',', ' ') }} F
                                </td>
                                <td></td>
                            </tr>

                            <!-- Lignes des reçus du service -->
                            @foreach($items as $billing)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $billing->receipt_number }}</td>
                                    <td class="small">{{ $billing->created_at ? $billing->created_at->format('d/m/Y H:i') : '---' }}</td>
                                    <td>
                                        <span class="badge {{ $meta['badge'] }} rounded-pill px-3" {!! isset($meta['style']) ? 'style="'.$meta['style'].'"' : '' !!}>
                                            {{ strtoupper($typeKey === 'car_wash' ? 'LAVAGE AUTO' : ($typeKey === 'show_ticket' ? 'SPECTACLE' : $typeKey)) }}
                                        </span>
                                    </td>
                                    <td class="small fw-semibold">
                                        @if($billing->service_type === 'restaurant' && $billing->order)
                                            Table n° {{ $billing->order->restaurantTable->number ?? 'Comptoir' }}
                                        @elseif($billing->service_type === 'show_ticket' && $billing->eventTicket && $billing->eventTicket->event)
                                            {{ $billing->eventTicket->event->title }}
                                        @elseif($billing->service_type === 'pressing')
                                            Client Pressing Externe
                                        @elseif($billing->service_type === 'car_wash')
                                            Client Lavage Auto Externe
                                        @else
                                            Client Comptoir
                                        @endif
                                    </td>
                                    <td><span class="badge bg-dark text-white rounded-pill px-2">{{ strtoupper($billing->payment_method) }}</span></td>
                                    <td class="text-end fw-bold text-success fs-6">{{ number_format($billing->amount_paid, 0, ',', ' ') }} F</td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('billing.print', $billing->id) }}" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-printer-fill me-1"></i> Imprimer
                                        </a>
                                        @role('Admin')
                                        <form action="{{ route('billing.payments.destroy', $billing->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce reçu ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill shadow-sm"><i class="bi bi-trash-fill"></i></button>
                                        </form>
                                        @endrole
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @empty
                    @endforelse

                    @if($directTransactions->count() === 0)
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">Aucun encaissement direct hors chambre sur cette période.</td>
                        </tr>
                    @endif
                </tbody>

                <!-- PIED DE TABLEAU POUR LES VENTES DIRECTES -->
                @if($directTransactions->count() > 0)
                    <tfoot style="background-color: #d1fae5;" class="border-top border-success border-2">
                        <tr class="fw-bold align-middle">
                            <td colspan="5" class="ps-4 py-3 text-uppercase small text-success">
                                <i class="bi bi-calculator-fill me-1"></i> Total Encaissements Directs Comptoir :
                            </td>
                            <td class="text-end text-success fs-6">{{ number_format($sumDirect, 0, ',', ' ') }} F</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

<!-- SCRIPT D'IMPRESSION AUTOMATIQUE APRÈS VALIDATION -->
@if(session('print_billing_id'))
<script>
    window.addEventListener('load', function() {
        window.open("{{ route('billing.print', session('print_billing_id')) }}", "_blank");
    });
</script>
@endif

@endsection
