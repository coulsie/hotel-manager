@extends('layouts.app')

@section('title', 'Planning RH, Pointage & Avances - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-people-fill text-primary me-2"></i>Ressources Humaines : Quarts, Pointage & Avances
            </h3>
            <p class="text-muted small mb-0">Organisation hebdomadaire des équipes, contrôle des présences du jour et suivi des acomptes.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hr.index', ['week_start' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-chevron-left"></i> Semaine préc.
            </a>
            <a href="{{ route('hr.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                Semaine actuelle
            </a>
            <a href="{{ route('hr.index', ['week_start' => $weekStart->copy()->addWeek()->toDateString()]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Semaine suiv. <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Cartes de Synthèse RH -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Effectif Actif</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ $staff->count() }} <small class="fs-6 fw-normal">collaborateur(s)</small></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Présents Aujourd'hui</div>
                    <h3 class="fw-bold mb-0 mt-1">
                        {{ $todayAttendances->whereIn('status', ['present', 'late'])->count() }} / {{ $staff->count() }}
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-white h-100" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
                <div class="card-body p-3">
                    <div class="small text-uppercase fw-bold opacity-75">Acomptes Accordés (Ce mois)</div>
                    <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalMonthAdvances, 0, ',', ' ') }} F</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- BLOC 1 : PLANNING HEBDOMADAIRE DES QUARTS DE TRAVAIL -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0 text-uppercase text-dark">
                <i class="bi bi-calendar-week text-primary me-2"></i>Planning Hebdomadaire ({{ $weekStart->format('d/m') }} au {{ $weekStart->copy()->addDays(6)->format('d/m/Y') }})
            </h6>
            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#assignShiftModal">
                <i class="bi bi-plus-circle text-warning me-1"></i> Planifier un Quart
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 small text-center">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="text-start ps-4" style="min-width: 180px;">Employé</th>
                            @foreach($days as $day)
                                <th class="{{ $day->isToday() ? 'bg-primary-subtle text-primary' : '' }}">
                                    <div>{{ $day->translatedFormat('D') }}</div>
                                    <div class="fw-bold">{{ $day->format('d/m') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $emp)
                            <tr>
                                <td class="text-start ps-4">
                                    <div class="fw-bold text-dark">{{ $emp->name }}</div>
                                    <span class="badge bg-secondary-subtle text-dark text-uppercase" style="font-size: 0.65rem;">
                                        {{ $emp->roles->first()->name ?? 'Agent' }}
                                    </span>
                                </td>
                                @foreach($days as $day)
                                    @php
                                        $key = $emp->id . '_' . $day->toDateString();
                                        $shift = isset($shifts[$key]) ? $shifts[$key]->first() : null;
                                        $badgeColor = match($shift->shift_type ?? '') {
                                            'matin'   => 'bg-info-subtle text-dark border-info',
                                            'soir'    => 'bg-warning-subtle text-dark border-warning',
                                            'nuit'    => 'bg-dark text-white',
                                            'journee' => 'bg-success-subtle text-success border-success',
                                            default   => '',
                                        };
                                    @endphp
                                    <td class="{{ $day->isToday() ? 'bg-light' : '' }}">
                                        @if($shift)
                                            <span class="badge border {{ $badgeColor }} d-block py-1">
                                                <strong>{{ strtoupper($shift->shift_type) }}</strong>
                                                <div style="font-size: 0.65rem;">{{ $shift->department }}</div>
                                            </span>
                                        @else
                                            <span class="text-muted opacity-50">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BLOC 2 : POINTAGE DU JOUR & AVANCES SUR SALAIRE -->
    <div class="row g-4">
        <!-- Pointage des présences du jour -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="fw-bold mb-0 text-uppercase text-dark">
                        <i class="bi bi-clock-fill text-success me-2"></i>Pointage du Jour ({{ \Carbon\Carbon::today()->format('d/m/Y') }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light text-uppercase text-muted">
                                <tr>
                                    <th class="ps-4">Collaborateur</th>
                                    <th>Arrivée</th>
                                    <th>Statut</th>
                                    <th class="text-end pe-4">Pointer</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff as $emp)
                                    @php
                                        $att = $todayAttendances->get($emp->id);
                                    @endphp
                                    <tr>
                                        <td class="ps-4 fw-bold text-dark">{{ $emp->name }}</td>
                                        <td>{{ $att && $att->clock_in ? \Carbon\Carbon::parse($att->clock_in)->format('H:i') : '--:--' }}</td>
                                        <td>
                                            @if(!$att)
                                                <span class="badge bg-light text-muted border">Non pointé</span>
                                            @elseif($att->status === 'present')
                                                <span class="badge bg-success">Présent</span>
                                            @elseif($att->status === 'late')
                                                <span class="badge bg-warning text-dark">En retard</span>
                                            @elseif($att->status === 'absent')
                                                <span class="badge bg-danger">Absent</span>
                                                                                        @else
                                                <span class="badge bg-secondary">Repos</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <form action="{{ route('hr.attendance.store') }}" method="POST" class="d-inline-flex gap-1">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $emp->id }}">
                                                <button type="submit" name="status" value="present" class="btn btn-sm btn-outline-success py-0 px-2" title="Présent">P</button>
                                                <button type="submit" name="status" value="late" class="btn btn-sm btn-outline-warning py-0 px-2" title="Retard">R</button>
                                                <button type="submit" name="status" value="absent" class="btn btn-sm btn-outline-danger py-0 px-2" title="Absent">A</button>
                                                <button type="submit" name="status" value="off" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Repos">Off</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- AVANCES SUR SALAIRE & ACOMPTES -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header text-white py-3 px-4 fw-bold" style="background: linear-gradient(135deg, #c2410c 0%, #ea580c 100%);">
                    <i class="bi bi-cash-coin text-warning me-2"></i>Accorder une Avance / Acompte
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('hr.advances.store') }}" method="POST" class="mb-4">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Employé bénéficiaire *</label>
                            <select name="user_id" class="form-select form-select-sm fw-bold" required>
                                @foreach($staff as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Montant (FCFA) *</label>
                                <input type="number" name="amount" min="500" step="500" class="form-control form-control-sm fw-bold" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Date *</label>
                                <input type="date" name="advance_date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-5">
                                <label class="form-label fw-bold small">Mode *</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Espèces">Espèces (Tiroir)</option>
                                    <option value="Wave">Wave</option>
                                    <option value="Orange Money">Orange Money</option>
                                </select>
                            </div>
                            <div class="col-7">
                                <label class="form-label fw-bold small">Motif</label>
                                <input type="text" name="reason" class="form-control form-control-sm" placeholder="Ex: Acompte quinzaine">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> Valider & Déduire de la Caisse
                        </button>
                    </form>

                    <h6 class="fw-bold small text-uppercase text-muted border-top pt-3 mb-2">Acomptes du Mois</h6>
                    <ul class="list-group list-group-flush small">
                        @forelse($monthAdvances as $adv)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                <div>
                                    <strong>{{ $adv->employee_name }}</strong>
                                    <span class="text-muted">({{ \Carbon\Carbon::parse($adv->advance_date)->format('d/m') }})</span>
                                </div>
                                <span class="fw-bold text-danger">- {{ number_format($adv->amount, 0, ',', ' ') }} F</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0">Aucun acompte ce mois-ci.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALE : PLANIFIER UN QUART DE TRAVAIL -->
<div class="modal fade" id="assignShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.shifts.store') }}" method="POST" class="modal-content rounded-4 border-0 shadow">
            @csrf
            <div class="modal-header bg-dark text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-calendar-plus text-warning me-2"></i>Planifier un Quart de Travail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Employé *</label>
                    <select name="user_id" class="form-select" required>
                        @foreach($staff as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">Service / Pôle *</label>
                        <select name="department" class="form-select" required>
                            <option value="Réception">Réception</option>
                            <option value="Restauration">Restauration / Bar</option>
                            <option value="Gouvernance">Gouvernance / Ménage</option>
                            <option value="Sécurité">Sécurité</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">Date *</label>
                        <input type="date" name="shift_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">Horaire / Quart *</label>
                    <select name="shift_type" class="form-select fw-bold" required>
                        <option value="matin">Matin (07h00 - 15h00)</option>
                        <option value="soir">Soir (15h00 - 23h00)</option>
                        <option value="nuit">Nuit (23h00 - 07h00)</option>
                        <option value="journee">Journée (08h00 - 17h00)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Enregistrer le Quart</button>
            </div>
        </form>
    </div>
</div>
@endsection

