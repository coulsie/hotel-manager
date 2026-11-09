@extends('layouts.app')

@section('title', 'Gestion des Habilitations - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-dark"><i class="bi bi-shield-lock text-primary me-2"></i>Personnel & Droits d'Accès</h1>
    <!-- Remplacer le bouton Ajouter par celui-ci -->
<button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#employeeModal" data-mode="create">
    <i class="bi bi-person-plus me-1"></i> Ajouter un Employé
</button>

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="card border-0 shadow-lg rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-dark text-uppercase small" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                <tr>
                    <th class="ps-4 py-3">Nom de l'Employé</th>
                    <th class="py-3">Adresse Email</th>
                    <th class="py-3">Rôle / Affectation</th>
                    <th class="text-end pe-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($employees as $employee)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">{{ $employee->name }}</td>
                        <td class="text-secondary">{{ $employee->email }}</td>
                        <td>
                            @php $roleName = $employee->roles->first()->name ?? 'Aucun'; @endphp
                            @if($roleName === 'admin')
                                <span class="badge bg-danger px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-person-badge me-1"></i> Administrateur
                                </span>
                            @elseif($roleName === 'receptionniste')
                                <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-front me-1"></i> Réception
                                </span>
                            @else
                                <span class="badge bg-success px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm">
                                    <i class="bi bi-cup-hot me-1"></i> Restauration
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">

                            <!-- Remplacer le bouton Habilitations par celui-ci -->
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4 fw-bold shadow-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#employeeModal"
                                    data-mode="edit"
                                    data-employee="{{ json_encode($employee) }}"
                                    data-role="{{ $roleName }}">
                                <i class="bi bi-gear me-1"></i> Habilitations
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <div class="mb-2"><i class="bi bi-people fs-1 text-secondary"></i></div>
                            <span class="fw-semibold">Aucun employé enregistré dans le système hôtelier.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- FENÊTRE MODALE UNIQUE POUR LA CRÉATION ET LA MODIFICATION DES DROITS -->
<div class="modal fade" id="employeeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fw-bold" id="employeeModalLabel">Compte Employé</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="employeeForm" method="POST" action="">
                @csrf
                <div id="method-container"></div>

                <div class="modal-body p-4">
                    <!-- Saisie Nom -->
                    <div class="mb-3">
                        <label for="modal_name" class="form-label fw-bold text-dark small text-uppercase">Nom Complet</label>
                        <input type="text" class="form-control fw-semibold" id="modal_name" name="name" placeholder="Ex: Koffi Emmanuel" required>
                    </div>

                    <!-- Saisie Email -->
                    <div class="mb-3">
                        <label for="modal_email" class="form-label fw-bold text-dark small text-uppercase">Adresse Email</label>
                        <input type="email" class="form-control" id="modal_email" name="email" placeholder="Ex: e.koffi@hotel.com" required>
                    </div>

                    <!-- Saisie Mot de passe (Conditionnel) -->
                    <div class="mb-3">
                        <label for="modal_password" class="form-label fw-bold text-dark small text-uppercase" id="pass-label">Mot de Passe</label>
                        <input type="password" class="form-control" id="modal_password" name="password" placeholder="Minimum 6 caractères">
                    </div>

                    <!-- Choix du Rôle (Table: roles via Spatie) -->
                    <div class="mb-0">
                        <label for="modal_role" class="form-label fw-bold text-dark small text-uppercase">Rôle & Droits Applicatifs</label>
                        <select class="form-select fw-bold text-primary" id="modal_role" name="role" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ strtoupper($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer bg-light border-top-0 p-3 d-flex justify-content-end gap-2 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-success px-4 rounded-pill fw-bold" id="btn-submit-text">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

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


