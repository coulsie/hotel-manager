@extends('layouts.app')

@section('title', 'Historique IoT - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-broadcast-pin text-primary me-2"></i>Journal de Traçabilité des <span style="color: #0891b2;">Accès IoT</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Surveillance en temps réel des serrures connectées et contrôle des badges RFID</p>
    </div>
    <span class="badge text-white px-3 py-2 rounded-pill fw-bold shadow-sm" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
        <i class="bi bi-wifi text-warning me-1"></i> Surveillance IoT Active
    </span>
</div>

@php
    $pageTotal   = $logs->count();
    $pageGranted = $logs->where('action', 'access_granted')->count();
    $pageDenied  = $logs->where('action', '!=', 'access_granted')->count();
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DE SÉCURITÉ (DÉGRADÉS ÉCLATANTS)                -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Total des passages (Page / Global) -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #1e3a8a 0%, #0891b2 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Scans Enregistrés</span>
                    <h2 class="fw-bold mb-0">{{ method_exists($logs, 'total') ? $logs->total() : $pageTotal }} <small class="fs-6 fw-normal">passage(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-cpu-fill fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Accès Autorisés -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Ouvertures Autorisées</span>
                    <h2 class="fw-bold mb-0">{{ $pageGranted }} <small class="fs-6 fw-normal">sur cette page</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-unlock-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Alertes & Refus -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #b91c1c 0%, #ef4444 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Accès Bloqués / Alertes</span>
                    <h2 class="fw-bold mb-0">{{ $pageDenied }} <small class="fs-6 fw-normal">sur cette page</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-shield-exclamation fs-3 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. JOURNAL DÉTAILLÉ DES SCANS RFID (DÉGRADÉ CYAN / INDIGO)            -->
<!-- ===================================================================== -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden mb-4">
    <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-rfid text-info me-2 fs-5"></i>Historique des scans de cartes magnétiques
        </h5>
        <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold shadow-sm">
            <i class="bi bi-record-circle-fill me-1"></i> Direct Serrures
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="small text-uppercase" style="background-color: #ecfeff; color: #155e75;">
                    <tr>
                        <th class="ps-4 py-3">Date & Heure du scan</th>
                        <th class="py-3">N° Chambre</th>
                        <th class="py-3">Code Unique RFID (Carte)</th>
                        <th class="py-3">Action détectée</th>
                        <th class="text-center pe-4 py-3">Statut de la serrure</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $isGranted = ($log->action === 'access_granted');
                        @endphp
                        <tr {!! !$isGranted ? 'style="background-color: #fef2f2;"' : '' !!}>
                            <td class="ps-4 small fw-semibold text-dark">
                                <i class="bi bi-clock-history text-primary me-1"></i>
                                {{ \Carbon\Carbon::parse($log->scanned_at)->format('d/m/Y à H:i:s') }}
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold fs-6">
                                    🚪 {{ $log->room->number ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-dark text-info font-monospace px-3 py-2 rounded-3 shadow-sm">
                                    <i class="bi bi-upc-scan me-1"></i>{{ $log->magneticCard->card_uid ?? 'UID INCONNU' }}
                                </span>
                            </td>
                            <td>
                                @if($isGranted)
                                    <span class="fw-semibold text-success">
                                        <i class="bi bi-unlock-fill me-2 fs-5"></i>Déverrouillage électronique
                                    </span>
                                @else
                                    <span class="fw-bold text-danger">
                                        <i class="bi bi-lock-fill me-2 fs-5"></i>Refus d'ouverture (Badge invalide ou expiré)
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                @if($isGranted)
                                    <span class="badge bg-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle-fill me-1"></i>Accès Autorisé
                                    </span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Accès Bloqué
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 bg-light text-muted">
                                <div class="mb-2"><i class="bi bi-broadcast fs-1 text-primary"></i></div>
                                <span class="fw-bold text-dark d-block">Aucun enregistrement IoT reçu pour le moment.</span>
                                <small>Les passages sur les serrures magnétiques apparaîtront ici automatiquement.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Liens de pagination de Laravel -->
<div class="d-flex justify-content-center">
    {{ $logs->links() }}
</div>
@endsection
