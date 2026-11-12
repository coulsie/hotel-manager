@extends('layouts.app')

@section('title', 'Gestion de l\'Entretien - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-stars text-warning me-2"></i>Registre & Planification de l'Entretien
        </h1>
        <p class="text-muted small mb-0 mt-1">Suivi de la propreté des chambres et affectation des équipes de ménage</p>
    </div>
    <!-- Bouton pour lancer l'impression de la fiche de travail matérielle -->
    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm d-print-none" onclick="window.print()">
        <i class="bi bi-printer-fill me-2"></i>Imprimer la Fiche de Ménage
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-print-none" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@php
    $countAvailable = $rooms->where('status', 'available')->count();
    $countCleaning  = $rooms->where('status', 'cleaning')->count();
    $countOccupied  = $rooms->where('status', 'occupied')->count();
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DE L'ENTRETIEN (DÉGRADÉS ÉCLATANTS)             -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4 d-print-none">
    <!-- Carte 1 : Chambres à Nettoyer (Priorité Ménage) -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">À Nettoyer / En cours</span>
                    <h2 class="fw-bold mb-0">{{ $countCleaning }} <small class="fs-6 fw-normal">chambre(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-brush-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Chambres Prêtes & Propres -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Prêtes (Disponibles)</span>
                    <h2 class="fw-bold mb-0">{{ $countAvailable }} <small class="fs-6 fw-normal">chambre(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-check2-circle fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Chambres Occupées -->
    <div class="col-md-4">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Actuellement Occupées</span>
                    <h2 class="fw-bold mb-0">{{ $countOccupied }} <small class="fs-6 fw-normal">chambre(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-door-closed-fill fs-3 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. TABLEAU D'AFFECTATION ET DE SUIVI DES CHAMBRES                     -->
<!-- ===================================================================== -->
<div class="card shadow border-0 rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);">
        <h5 class="card-title mb-0 fw-bold">
            <i class="bi bi-clipboard2-check text-warning me-2"></i>Rapport d'affectation des chambres
        </h5>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ $rooms->count() }} chambres au total</span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="print-zone">
                <thead class="small text-uppercase" style="background-color: #eef2ff; color: #3730a3;">
                    <tr>
                        <th class="ps-4 py-3">N° Chambre</th>
                        <th class="py-3">Catégorie</th>
                        <th class="py-3">État Actuel</th>
                        <th class="text-center py-3 d-none d-print-table-cell">Visa / Contrôle Ménage</th>
                        <th class="text-center pe-4 py-3 d-print-none">Changer le statut (Action)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        @php
                            $rowHighlight = '';
                            $badgeStyle   = 'bg-success text-white';
                            $statusIcon   = 'bi-check-circle-fill';
                            $statusLabel  = 'Disponible';

                            if ($room->status === 'occupied') {
                                $badgeStyle  = 'bg-danger text-white';
                                $statusIcon  = 'bi-door-closed-fill';
                                $statusLabel = 'Occupée';
                            } elseif ($room->status === 'cleaning') {
                                $rowHighlight = 'style="background-color: #fffbeb;"';
                                $badgeStyle   = 'bg-warning text-dark border border-warning';
                                $statusIcon   = 'bi-brush-fill';
                                $statusLabel  = 'En Nettoyage';
                            }
                        @endphp
                        <tr {!! $rowHighlight !!}>
                            <td class="ps-4">
                                <span class="fw-bold text-primary fs-6">🚪 {{ $room->number }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle rounded-pill px-3 py-1 fw-semibold text-uppercase" style="font-size: 0.72rem;">
                                    {{ $room->type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $badgeStyle }} rounded-pill px-3 py-2 fw-bold text-uppercase shadow-sm" style="font-size: 0.75rem;">
                                    <i class="bi {{ $statusIcon }} me-1"></i> {{ $statusLabel }}
                                </span>
                            </td>
                            <!-- Colonne visible uniquement sur la feuille imprimée pour signer -->
                            <td class="text-center d-none d-print-table-cell text-muted small">
                                [ &nbsp;&nbsp; ] Fait &nbsp;&nbsp;&nbsp; Signature : __________
                            </td>
                            <td class="text-center pe-4 d-print-none">
                                <!-- Formulaire rapide de changement de statut -->
                                <form action="{{ route('housekeeping.update', $room->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="input-group input-group-sm justify-content-center">
                                        <select name="status" class="form-select form-select-sm fw-semibold border-primary-subtle" style="max-width: 175px;" required>
                                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>✅ Disponible (Propre)</option>
                                            <option value="cleaning" {{ $room->status === 'cleaning' ? 'selected' : '' }}>🧹 En Nettoyage</option>
                                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>🔴 Occupée</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary fw-bold px-3" title="Mettre à jour le statut">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Styles d'impression CSS pour masquer le menu et les formulaires sur le papier -->
<style>
    @media print {
        #sidebarMenu, header, .navbar, .btn, .d-print-none, form {
            display: none !important;
        }
        main, .col-md-9 {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        table {
            width: 100% !important;
            border: 1px solid #000 !important;
        }
        .badge {
            border: 1px solid #000 !important;
            color: #000 !important;
            background: transparent !important;
        }
    }
</style>
@endsection
