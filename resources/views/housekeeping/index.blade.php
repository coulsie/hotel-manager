@extends('layouts.app')

@section('title', 'Gestion de l\'Entretien - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-brush me-2"></i>Registre & Planification de l'Entretien</h1>
    <!-- Bouton pour lancer l'impression de la fiche de travail matérielle -->
    <button class="btn btn-dark shadow-sm" onclick="window.print()">
        <i class="bi bi-printer-fill me-2"></i>Imprimer la Fiche de Ménage
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="card-title mb-0">Rapport d'affectation des chambres</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0" id="print-zone">
                <thead class="table-light">
                    <tr>
                        <th>N° Chambre</th>
                        <th>Catégorie</th>
                        <th>État Actuel</th>
                        <th class="text-center d-print-none">Changer le statut (Action)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        @php
                            $badgeColor = 'bg-success';
                            if($room->status === 'occupied') $badgeColor = 'bg-danger';
                            if($room->status === 'cleaning') $badgeColor = 'bg-warning text-dark';
                        @endphp
                        <tr>
                            <td><strong>{{ $room->number }}</strong></td>
                            <td>{{ $room->type }}</td>
                            <td>
                                <span class="badge {{ $badgeColor }} text-uppercase">
                                    @if($room->status === 'available') Disponible
                                    @elseif($room->status === 'occupied') Occupée
                                    @else En Nettoyage
                                    @endif
                                </span>
                            </td>
                            <td class="text-center d-print-none">
                                <!-- Formulaire rapide de changement de statut -->
                                <form action="{{ route('housekeeping.update', $room->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <div class="input-group input-group-sm justify-content-center">
                                        <select name="status" class="form-select form-select-sm" style="max-width: 150px;" required>
                                            <option value="available" {{ $room->status === 'available' ? 'selected' : '' }}>Disponible (Propre)</option>
                                            <option value="cleaning" {{ $room->status === 'cleaning' ? 'selected' : '' }}>En Nettoyage</option>
                                            <option value="occupied" {{ $room->status === 'occupied' ? 'selected' : '' }}>Occupée</option>
                                        </select>
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-clockwise"></i>
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
    }
</style>
@endsection
