@extends('layouts.app')

@section('title', 'Gouvernance & Ménage - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-stars text-warning me-2"></i>Gouvernance, Recouche & Maintenance
            </h3>
            <p class="text-muted small mb-0">Gestion des départs, recouche des séjours en cours, signalement d'incidents et traçabilité.</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#weeklyPlanningModal">
            <i class="bi bi-calendar-week-fill me-1"></i> Planning Hebdomadaire
        </button>
        
        <a href="{{ route('housekeeping.report') }}" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-file-earmark-bar-graph me-1 text-warning"></i> Rapport & Audit
        </a>

        <a href="{{ route('reservations.rack') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Voir le Rack Hôtelier
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Compteurs d'états -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">À Nettoyer (Départs)</div>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['cleaning'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Recouches (Occupées)</div>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['occupied'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Prêtes / Propres</div>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['available'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">En Maintenance</div>
                    <h2 class="fw-bold mb-0 mt-1">{{ $stats['maintenance'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de Programmation Journalière & Équipe d'Entretien -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="fw-bold mb-0 text-uppercase text-primary">
                <i class="bi bi-people-fill me-2"></i>Programmation Journalière & Attribution des Agents ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }})
            </h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#newAgentModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Nouvel Agent
                </button>
                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-printer me-1"></i> Imprimer Feuille de Route
                </button>
            </div>
        </div>
        <div class="card-body p-3 bg-light">
            <form action="{{ route('housekeeping.assign') }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Date du planning</label>
                    <input type="date" name="work_date" value="{{ $selectedDate }}" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold mb-1">Chambre à nettoyer</label>
                    <select name="room_id" class="form-select form-select-sm fw-bold" required>
                        @foreach($rooms as $r)
                            <option value="{{ $r->id }}">Ch. {{ $r->number }} ({{ strtoupper($r->status) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold mb-1">Agent responsable</label>
                    <select name="housekeeper_id" class="form-select form-select-sm" required>
                        <option value="">-- Choisir un agent --</option>
                        @foreach($agents as $ag)
                            <option value="{{ $ag->id }}">{{ $ag->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-pill fw-bold">
                        <i class="bi bi-check-lg me-1"></i> Attribuer
                    </button>
                </div>
            </form>
        </div>
    </div>


       <!-- Grille des Chambres -->
    <div class="row g-3 mb-5">
        @foreach($rooms as $room)
            @php
                $isDailyCleaned = in_array($room->id, $dailyCleanedRoomIds);
                $incident = $activeIncidents->get($room->id);
                $schedule = $todaySchedules->get($room->id);

                $badgeClass = match($room->status) {
                    'cleaning'    => 'bg-warning text-dark',
                    'available'   => 'bg-success text-white',
                    'occupied'    => 'bg-primary text-white',
                    'maintenance' => 'bg-danger text-white',
                    default       => 'bg-light text-dark',
                };
                $statusLabel = match($room->status) {
                    'cleaning'    => 'À nettoyer',
                    'available'   => 'Prête / Propre',
                    'occupied'    => 'Occupée',
                    'maintenance' => 'Maintenance',
                    default       => ucfirst($room->status),
                };
                $borderStyle = match($room->status) {
                    'cleaning'    => 'border: 2px solid #f59e0b;',
                    'maintenance' => 'border: 2px solid #ef4444;',
                    default       => '',
                };
            @endphp

            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card shadow-sm rounded-4 h-100 border-0" style="{{ $borderStyle }}">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="fw-bold mb-0">Chambre {{ $room->number }}</h5>
                                    <span class="text-muted small d-block">{{ $room->type }}</span>

                                    <!-- Nom de l'agent d'entretien attribué aujourd'hui -->
                                    @if($schedule)
                                        <div class="mt-1 small fw-bold text-primary">
                                            <i class="bi bi-person-badge-fill me-1"></i>Entretien : <span class="text-uppercase">{{ $schedule->housekeeper_name }}</span>
                                        </div>
                                    @else
                                        <div class="mt-1 small text-muted fst-italic" style="font-size: 0.75rem;">
                                            <i class="bi bi-person-dash me-1"></i>Entretien : Non attribué
                                        </div>
                                    @endif
                                </div>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <!-- Affichage de la note d'incident si en maintenance -->
                            @if($room->status === 'maintenance' && $incident)
                                <div class="alert alert-danger py-1 px-2 small mb-2 mt-2 rounded-3">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $incident->notes }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 pt-2 border-top d-flex gap-2 align-items-center">
                            @if($room->status === 'cleaning')
                                <form action="{{ route('housekeeping.clean', $room->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-check2-circle me-1"></i> Valider Ménage
                                    </button>
                                </form>
                            @elseif($room->status === 'occupied')
                                @if($isDailyCleaned)
                                    <div class="d-flex align-items-center justify-content-between gap-1 flex-grow-1">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 flex-grow-1 text-center">
                                            <i class="bi bi-check-all me-1"></i> Recouche faite
                                        </span>
                                        <form action="{{ route('housekeeping.undo.daily.clean', $room->id) }}" method="POST" onsubmit="return confirm('Annuler la validation de recouche pour cette chambre ?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary rounded-circle px-2 py-0" title="Annuler et revenir en attente">
                                                <i class="bi bi-arrow-counterclockwise"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('housekeeping.daily.clean', $room->id) }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold">
                                            <i class="bi bi-brush me-1"></i> Recouche du jour
                                        </button>
                                    </form>
                                @endif

                            @elseif($room->status === 'maintenance')
                                <form action="{{ route('housekeeping.resolve.maintenance', $room->id) }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning w-100 rounded-pill fw-bold shadow-sm">
                                        <i class="bi bi-wrench-adjustable me-1"></i> Réparation OK
                                    </button>
                                </form>
                            @else
                                <div class="text-success small fw-semibold flex-grow-1">
                                    <i class="bi bi-shield-check me-1"></i> Prête pour client
                                </div>
                            @endif

                            <!-- Bouton Signalement Incident (si non occupée et pas déjà en maintenance) -->
                            @if($room->status !== 'occupied' && $room->status !== 'maintenance')
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#incidentModal"
                                        data-room-id="{{ $room->id }}"
                                        data-room-number="{{ $room->number }}"
                                        title="Signaler un incident technique">
                                    <i class="bi bi-tools"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


        <!-- Journal de Traçabilité des Interventions -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>Journal de Traçabilité (Ménage & Incidents)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Date & Heure</th>
                            <th>Chambre</th>
                            <th>Opération</th>
                            <th>Détails / Observation</th>
                            <th class="pe-4">Agent / Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                            <tr>
                                <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-primary">Ch. {{ $log->room_number }}</td>
                                <td>
                                    @if($log->action_type === 'checkout_clean')
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Nettoyage Départ</span>
                                    @elseif($log->action_type === 'daily_stay_clean')
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2">Recouche Séjour</span>
                                    @elseif($log->action_type === 'maintenance_report')
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2">Incident Signalé</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark rounded-pill px-2">Fin Maintenance</span>
                                    @endif
                                </td>
                                <td>{{ $log->notes }}</td>
                                <td class="pe-4 fw-semibold">{{ $log->agent_name ?? 'Système / Réception' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Aucune intervention enregistrée pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Fenêtre Modale : Signalement d'Incident Technique -->
<div class="modal fade" id="incidentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="incidentForm" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Signaler un Incident - Chambre <span id="modalRoomNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label fw-bold small">Description de la panne ou du problème *</label>
                <textarea name="notes" rows="3" class="form-control" placeholder="Ex: Climatiseur ne refroidit plus, fuite lavabo, ampoule grillée..." required></textarea>
                <div class="form-text small text-muted mt-2">
                    La chambre sera immédiatement bloquée en statut <strong>Maintenance</strong> jusqu'à résolution.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Bloquer en Maintenance</button>
            </div>
        </form>
    </div>
</div>

<!-- Fenêtre Modale : Ajouter un Nouvel Agent d'entretien -->
<div class="modal fade" id="newAgentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('housekeeping.agents.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Ajouter un Agent d'Entretien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Nom & Prénoms de l'agent *</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Ex: Koné Mariam" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Téléphone (Optionnel)</label>
                    <input type="text" name="phone" class="form-control" placeholder="Ex: 07 00 00 00 00">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer l'agent</button>
            </div>
        </form>
    </div>
</div>
<!-- Fenêtre Modale : Tableau de Programmation Hebdomadaire des Agents -->
<div class="modal fade" id="weeklyPlanningModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <form action="{{ route('housekeeping.assign.week') }}" method="POST" class="modal-content rounded-4 border-0 shadow-lg">
            @csrf
            <div class="modal-header text-white rounded-top-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-calendar-week-fill text-warning me-2"></i>Planning Hebdomadaire des Agents d'Entretien
                    </h5>
                    <div class="small text-white-50">
                        Semaine du {{ $weekStart->format('d/m/Y') }} au {{ $weekEnd->format('d/m/Y') }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" onclick="window.print()" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">
                        <i class="bi bi-printer me-1"></i> Imprimer Planning
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0 text-center small">
                        <thead class="table-light text-uppercase">
                            <tr>
                                <th class="text-start ps-3" style="min-width: 140px;">Chambre</th>
                                @foreach($weekDays as $day)
                                    <th class="{{ $day->isToday() ? 'bg-warning-subtle text-dark' : '' }}" style="min-width: 145px;">
                                        <div class="fw-bold">{{ ucfirst($day->translatedFormat('l')) }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $day->format('d/m') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rooms as $room)
                                <tr>
                                    <td class="text-start ps-3 fw-bold bg-light">
                                        <div>Ch. {{ $room->number }}</div>
                                        <span class="text-muted fw-normal" style="font-size: 0.72rem;">{{ $room->type }}</span>
                                    </td>
                                    @foreach($weekDays as $day)
                                        @php
                                            $dateStr = $day->toDateString();
                                            $entry = $weekMatrix[$room->id . '_' . $dateStr] ?? null;
                                            $isDone = $entry && $entry->status === 'done';
                                        @endphp
                                        <td class="p-1 {{ $day->isToday() ? 'bg-warning-subtle' : '' }}">
                                            <select name="assignments[{{ $room->id }}][{{ $dateStr }}]"
                                                    class="form-select form-select-sm {{ $entry ? ($isDone ? 'border-success bg-success-subtle fw-bold' : 'border-primary fw-semibold') : 'text-muted' }}"
                                                    style="font-size: 0.78rem;">
                                                <option value="">-- Libre --</option>
                                                @foreach($agents as $agent)
                                                    <option value="{{ $agent->id }}" {{ ($entry && $entry->housekeeper_id == $agent->id) ? 'selected' : '' }}>
                                                        {{ $agent->full_name }} {{ $isDone && $entry->housekeeper_id == $agent->id ? '✓' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light rounded-bottom-4 d-flex justify-content-between">
                <div class="small text-muted">
                    <span class="badge bg-success-subtle text-success border border-success-subtle me-1">✓</span> Tâche déjà validée par l'agent
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Enregistrer toute la semaine
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const incidentModal = document.getElementById('incidentModal');
        if (incidentModal) {
            incidentModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const roomId = button.getAttribute('data-room-id');
                const roomNumber = button.getAttribute('data-room-number');

                document.getElementById('modalRoomNumber').textContent = roomNumber;
                document.getElementById('incidentForm').action = '/housekeeping/' + roomId + '/incident';
            });
        }
    });
</script>
@endsection
