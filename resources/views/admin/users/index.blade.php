@extends('layouts.app')

@section('title', 'Gestion des Habilitations - Hotel Manager')

@section('content')
<!-- En-tête de la page -->
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-3 mb-4 border-bottom">
    <div>
        <h1 class="h2 fw-bold text-dark mb-0">
            <i class="bi bi-shield-lock-fill text-primary me-2"></i>Personnel & <span style="color: #4f46e5;">Droits d'Accès</span>
        </h1>
        <p class="text-muted small mb-0 mt-1">Administration des comptes collaborateurs, affectation des services et habilitations</p>
    </div>
    <button type="button" class="btn text-white rounded-pill px-4 py-2 fw-bold shadow" data-bs-toggle="modal" data-bs-target="#employeeModal" data-mode="create" style="background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); border: none;">
        <i class="bi bi-person-plus-fill text-warning me-2"></i>Ajouter un Employé
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 p-3" role="alert" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i><strong>{{ session('success') }}</strong>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Veuillez corriger les erreurs :</div>
        @foreach($errors->all() as $error)
            <div class="small">• {{ $error }}</div>
        @endforeach
    </div>
@endif

@php
    $totalEmployees = $employees->count();
    $countAdmins    = $employees->filter(fn($e) => strtolower($e->roles->first()->name ?? '') === 'admin')->count();
    $countReception = $employees->filter(fn($e) => strtolower($e->roles->first()->name ?? '') === 'receptionniste')->count();
    $countResto     = $employees->filter(fn($e) => !in_array(strtolower($e->roles->first()->name ?? ''), ['admin', 'receptionniste', '']))->count();
@endphp

<!-- ===================================================================== -->
<!-- 1. CARTES DE SYNTHÈSE DES EFFECTIFS (DÉGRADÉS ÉCLATANTS)              -->
<!-- ===================================================================== -->
<div class="row g-3 mb-4">
    <!-- Carte 1 : Effectif Total -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Effectif Total</span>
                    <h2 class="fw-bold mb-0">{{ $totalEmployees }} <small class="fs-6 fw-normal">agent(s)</small></h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-people-fill fs-4 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 2 : Administrateurs -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Administrateurs</span>
                    <h2 class="fw-bold mb-0">{{ $countAdmins }}</h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-shield-fill-check fs-4 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 3 : Réception -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Pôle Réception</span>
                    <h2 class="fw-bold mb-0">{{ $countReception }}</h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-door-open-fill fs-4 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte 4 : Restauration & Services -->
    <div class="col-md-6 col-xl-3">
        <div class="card border-0 shadow rounded-4 h-100 text-white" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase fw-bold small opacity-75 d-block">Restauration & Bar</span>
                    <h2 class="fw-bold mb-0">{{ $countResto }}</h2>
                </div>
                <div class="bg-white bg-opacity-25 rounded-circle p-3">
                    <i class="bi bi-cup-hot-fill fs-4 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 2. TABLEAU COLORÉ DU PERSONNEL & HABILITATIONS                        -->
<!-- ===================================================================== -->
<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <div class="card-header text-white fw-bold py-3 px-4 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
        <span><i class="bi bi-person-lines-fill text-warning me-2 fs-5"></i>Annuaire des Comptes & Habilitations</span>
        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold">{{ $totalEmployees }} compte(s) actif(s)</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="small text-uppercase" style="background-color: #eff6ff; color: #1e40af; font-size: 0.8rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">Nom de l'Employé</th>
                    <th class="py-3">Adresse Email</th>
                    <th class="py-3">Rôle / Affectation</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($employees as $employee)
                    @php
                        $roleName = $employee->roles->first()->name ?? 'Aucun';
                        $roleLower = strtolower($roleName);
                        $avatarBg = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                        if ($roleLower === 'admin') {
                            $avatarBg = 'linear-gradient(135deg, #ef4444 0%, #b91c1c 100%)';
                        } elseif ($roleLower === 'receptionniste') {
                            $avatarBg = 'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)';
                        }
                    @endphp
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center shadow-sm flex-shrink-0" style="width: 40px; height: 40px; background: {{ $avatarBg }};">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>
                                <div class="fw-bold text-dark fs-6">{{ $employee->name }}</div>
                            </div>
                        </td>
                        <td class="text-secondary fw-medium">
                            <i class="bi bi-envelope-fill text-primary opacity-75 me-1"></i>{{ $employee->email }}
                        </td>
                        <td>
                            @if($roleLower === 'admin')
                                <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-shield-fill-check me-1"></i> Administrateur
                                </span>
                            @elseif($roleLower === 'receptionniste')
                                <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-door-open-fill me-1"></i> Réception
                                </span>
                            @else
                                <span class="badge bg-success px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-cup-hot-fill me-1"></i> Restauration
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#employeeModal"
                                    data-mode="edit"
                                    data-employee="{{ json_encode($employee) }}"
                                    data-role="{{ $roleName }}">
                                <i class="bi bi-gear-fill me-1"></i> Habilitations
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 bg-light text-muted">
                            <div class="mb-2"><i class="bi bi-people fs-1 text-primary"></i></div>
                            <span class="fw-bold text-dark d-block">Aucun employé enregistré dans le système hôtelier.</span>
                            <small>Cliquez sur « Ajouter un Employé » pour créer le premier accès.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ===================================================================== -->
<!-- 3. FENÊTRE MODALE UNIQUE POUR LA CRÉATION ET LA MODIFICATION          -->
<!-- ===================================================================== -->
<div class="modal fade" id="employeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white py-3 px-4 border-0" style="background: linear-gradient(135deg, #1e3a8a 0%, #4f46e5 100%);">
                <h5 class="modal-title fw-bold" id="employeeModalLabel">
                    <i class="bi bi-person-badge-fill text-warning me-2"></i>Compte Employé
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="employeeForm" method="POST" action="">
                @csrf
                <div id="method-container"></div>

                <div class="modal-body p-4" style="background-color: #f8fafc;">
                    <!-- Saisie Nom -->
                    <div class="mb-3">
                        <label for="modal_name" class="form-label fw-bold text-dark small text-uppercase">Nom Complet</label>
                        <input type="text" class="form-control fw-semibold border-primary-subtle" id="modal_name" name="name" placeholder="Ex: Koffi Emmanuel" required>
                    </div>

                    <!-- Saisie Email -->
                    <div class="mb-3">
                        <label for="modal_email" class="form-label fw-bold text-dark small text-uppercase">Adresse Email</label>
                        <input type="email" class="form-control border-primary-subtle" id="modal_email" name="email" placeholder="Ex: e.koffi@hotel.com" required>
                    </div>

                    <!-- Saisie Mot de passe (Conditionnel) -->
                    <div class="mb-3">
                        <label for="modal_password" class="form-label fw-bold text-dark small text-uppercase" id="pass-label">Mot de Passe</label>
                        <input type="password" class="form-control border-primary-subtle" id="modal_password" name="password" placeholder="Minimum 6 caractères">
                    </div>

                    <!-- Choix du Rôle (Table: roles via Spatie) -->
                    <div class="mb-0">
                        <label for="modal_role" class="form-label fw-bold text-dark small text-uppercase">Rôle & Droits Applicatifs</label>
                        <select class="form-select fw-bold text-primary border-primary-subtle" id="modal_role" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn text-white px-4 rounded-pill fw-bold shadow-sm" id="btn-submit-text" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none;">
                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CONSERVEZ VOTRE BLOC <script> ... </script>@endsection EXISTANT CI-DESSOUS --}}

<!-- INJECTION DU SCRIPT VISUEL SANS INTERFÉRENCE AVEC LE COMPILATEUR BLADE -->

<!-- LOGIQUE JAVASCRIPT DE COMMUTATION DES HABILITATIONS CORRIGÉE -->
<!-- LOGIQUE DE CONFIGURATION AUTOMATIQUE COMPATIBLE VITE & BOOTSTRAP 5 -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalElement = document.getElementById('employeeModal');
    if (!modalElement) return;

    // Cet événement se déclenche AUTOMATIQUEMENT dès que la modale commence à s'ouvrir
    modalElement.addEventListener('show.bs.modal', function (event) {
        // Le bouton qui a déclenché l'ouverture
        var button = event.relatedTarget;
        if (!button) return;

        // Extraction des attributs de données du bouton
        var mode = button.getAttribute('data-mode');

        var form = document.getElementById('employeeForm');
        var label = document.getElementById('employeeModalLabel');
        var method = document.getElementById('method-container');
        var submitBtn = document.getElementById('btn-submit-text');
        var passLabel = document.getElementById('pass-label');

        var inputName = document.getElementById('modal_name');
        var inputEmail = document.getElementById('modal_email');
        var inputPassword = document.getElementById('modal_password');
        var inputRole = document.getElementById('modal_role');

        if (mode === 'edit') {
            // --- MODE MODIFICATION ---
            var employeeData = JSON.parse(button.getAttribute('data-employee'));
            var currentRole = button.getAttribute('data-role') || '';

            if (label) label.innerHTML = '<i class="bi bi-shield-exclamation text-warning me-2"></i>Modifier les droits de : ' + employeeData.name;
            if (submitBtn) {
                submitBtn.textContent = "Mettre à jour les droits";
                submitBtn.className = "btn btn-warning px-4 rounded-pill fw-bold text-dark";
            }
            if (passLabel) passLabel.textContent = "Changer le Mot de Passe (Laissez vide pour conserver)";
            if (form) form.action = "{{ url('admin/users') }}/" + employeeData.id;
            if (method) method.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            if (inputName) inputName.value = employeeData.name;
            if (inputEmail) inputEmail.value = employeeData.email;
            if (inputPassword) {
                inputPassword.value = '';
                inputPassword.required = false;
            }
            if (inputRole) inputRole.value = currentRole;
        } else {
            // --- MODE CRÉATION ---
            if (label) label.innerHTML = '<i class="bi bi-person-plus text-success me-2"></i>Créer un nouveau compte personnel';
            if (submitBtn) {
                submitBtn.textContent = "Créer le compte";
                submitBtn.className = "btn btn-success px-4 rounded-pill fw-bold";
            }
            if (passLabel) passLabel.textContent = "Mot de Passe";
            if (form) form.action = "{{ route('admin.users.store') }}";
            if (method) method.innerHTML = '';

            if (inputName) inputName.value = '';
            if (inputEmail) inputEmail.value = '';
            if (inputPassword) {
                inputPassword.value = '';
                inputPassword.required = true;
            }
            if (inputRole) inputRole.selectedIndex = 0;
        }
    });
});
</script>
@endsection


