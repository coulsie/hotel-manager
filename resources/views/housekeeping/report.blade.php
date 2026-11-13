@extends('layouts.app')

@section('title', 'Rapport & Audit Gouvernance - Hotel Manager')

@section('content')
<style>
    @media print {
        .no-print, .navbar, .sidebar { display: none !important; }
        .card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; }
        .print-dark { color: #000 !important; background: #fff !important; }
    }
</style>

<div class="container-fluid py-3">
    <!-- En-tête avec dégradé Indigo / Bleu Royal -->
    <div class="card border-0 shadow rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-4 text-white d-flex justify-content-between align-items-center flex-wrap gap-3 print-dark"
             style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #0284c7 100%);">
            <div>
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-shield-check text-warning me-2"></i>Rapport & Audit des Entretiens de Chambres
                </h3>
                <p class="small mb-0 text-white-50 print-dark">
                    Période du <strong class="text-white print-dark">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}</strong> au <strong class="text-white print-dark">{{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}</strong> • Traçabilité complète des interventions
                </p>
            </div>
            <div class="d-flex gap-2 no-print">
                <a href="{{ route('housekeeping.index') }}" class="btn btn-light text-primary rounded-pill px-3 fw-bold shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Retour Gouvernance
                </a>
                <button type="button" onclick="window.print()" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-printer-fill me-1"></i> Imprimer le Rapport
                </button>
            </div>
        </div>
    </div>

    <!-- Formulaire de Filtres (Enquête & Contrôle Qualité) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print" style="border-top: 4px solid #f97316 !important;">
        <div class="card-body p-3 bg-white rounded-4">
            <form action="{{ route('housekeeping.report') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary mb-1">Du *</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control form-control-sm border-primary-subtle" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary mb-1">Au *</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control form-control-sm border-primary-subtle" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary mb-1">Chambre</label>
                    <select name="room_id" class="form-select form-select-sm border-primary-subtle">
                        <option value="">Toutes les chambres</option>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}" {{ $roomId == $r->id ? 'selected' : '' }}>Ch. {{ $r->number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-primary mb-1">Agent d'entretien</label>
                    <select name="housekeeper_name" class="form-select form-select-sm border-primary-subtle">
                        <option value="">Tous les agents</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->full_name }}" {{ $agent == $ag->full_name ? 'selected' : '' }}>{{ $ag->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-primary mb-1">Type d'action</label>
                    <select name="action_type" class="form-select form-select-sm border-primary-subtle">
                        <option value="">Toutes opérations</option>
                        <option value="checkout_clean" {{ $type == 'checkout_clean' ? 'selected' : '' }}>Nettoyage Départ</option>
                        <option value="daily_stay_clean" {{ $type == 'daily_stay_clean' ? 'selected' : '' }}>Recouche Séjour</option>
                        <option value="maintenance_report" {{ $type == 'maintenance_report' ? 'selected' : '' }}>Incident / Panne</option>
                        <option value="maintenance_resolved" {{ $type == 'maintenance_resolved' ? 'selected' : '' }}>Fin Maintenance</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-sm text-white w-100 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                        <i class="bi bi-funnel-fill me-1"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bilan par Agent sur la période (Cartes colorées dynamiques) -->
    @if($agentSummary->isNotEmpty())
        @php
            $cardGradients = [
                'linear-gradient(135deg, #059669 0%, #10b981 100%)', // Émeraude
                'linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%)', // Violet
                'linear-gradient(135deg, #ea580c 0%, #f97316 100%)', // Orange vif
                'linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%)', // Cyan
            ];
        @endphp
        <div class="row g-3 mb-4">
            @foreach($agentSummary as $sum)
                @php
                    $gradient = $cardGradients[$loop->index % count($cardGradients)];
                @endphp
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow rounded-4 h-100 text-white print-dark" style="background: {{ $gradient }};">
                        <div class="card-body p-3">
                            <div class="fw-bold text-uppercase small opacity-90 mb-1">
                                <i class="bi bi-person-badge-fill text-warning me-1"></i>{{ $sum['name'] }}
                            </div>
                            <h3 class="fw-bold mb-2">
                                {{ $sum['total'] }} <small class="fs-6 fw-normal opacity-75">intervention(s)</small>
                            </h3>
                            <div class="d-flex gap-2 small">
                                <span class="badge bg-white text-dark rounded-pill px-2">
                                    Départs : <strong>{{ $sum['checkout_clean'] }}</strong>
                                </span>
                                <span class="badge bg-white text-dark rounded-pill px-2">
                                    Recouches : <strong>{{ $sum['daily_clean'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tableau Détaillé Horodaté -->
    <div class="card border-0 shadow rounded-4 overflow-hidden">
        <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center print-dark"
             style="background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);">
            <h6 class="fw-bold mb-0 text-uppercase">
                <i class="bi bi-clock-history text-warning me-2"></i>Historique Chronologique des Accès & Entretiens
            </h6>
            <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold shadow-sm">
                {{ $logs->count() }} enregistrement(s)
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="text-uppercase" style="background-color: #f0fdfa; color: #0f766e;">
                        <tr>
                            <th class="ps-4 py-3">Date & Heure Exacte</th>
                            <th class="py-3">Chambre</th>
                            <th class="py-3">Nature de l'intervention</th>
                            <th class="py-3">Agent Responsable</th>
                            <th class="py-3">Observation / Détails</th>
                            <th class="pe-4 py-3">Validé par (Réception)</th>
                        </tr>
                    </thead>
                                       <tbody>
                        @forelse($logs as $log)
                            @php
                                $rowBg = match($log->action_type) {
                                    'maintenance_report'   => 'background-color: #fef2f2;',
                                    'maintenance_resolved' => 'background-color: #fffbeb;',
                                    default                => '',
                                };
                            @endphp
                            <tr style="{{ $rowBg }}">
                                <td class="ps-4 fw-semibold text-dark">
                                    <i class="bi bi-calendar2-event text-primary me-1"></i>
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y à H:i:s') }}
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-1 text-white" style="background-color: #2563eb;">
                                        Ch. {{ $log->room_number }}
                                    </span>
                                    <span class="text-muted ms-1">{{ $log->room_type }}</span>
                                </td>
                                <td>
                                    @if($log->action_type === 'checkout_clean')
                                        <span class="badge bg-success text-white rounded-pill px-3 py-1">
                                            <i class="bi bi-check-circle-fill me-1"></i>Nettoyage Départ
                                        </span>
                                    @elseif($log->action_type === 'daily_stay_clean')
                                        <span class="badge bg-info text-dark rounded-pill px-3 py-1">
                                            <i class="bi bi-brush-fill me-1"></i>Recouche Quotidienne
                                        </span>
                                    @elseif($log->action_type === 'maintenance_report')
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Incident Signalé
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                            <i class="bi bi-wrench-adjustable me-1"></i>Fin Maintenance
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->housekeeper_name)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold text-uppercase">
                                            <i class="bi bi-person-fill me-1"></i>{{ $log->housekeeper_name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-dark">{{ $log->notes }}</td>
                                <td class="pe-4 fw-semibold text-secondary">
                                    <i class="bi bi-shield-lock me-1 text-muted"></i>{{ $log->supervisor_name ?? 'Système' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 text-secondary"></i>
                                    Aucun entretien ou incident enregistré sur cette période avec ces critères.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
