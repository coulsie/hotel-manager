<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Hotel Manager</title>
    <!-- Chargement de Bootstrap via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Icônes Bootstrap -->
    <link rel="stylesheet" href="https://jsdelivr.net">

    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            background-color: #ffffff;
        }
        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            transition: all 0.2s;
        }
        .btn-login:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }
        .toggle-password {
            cursor: pointer;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-4">

    <div class="container" style="max-width: 450px;">

        <!-- Logo ou Titre de l'établissement -->
        <div class="text-center mb-4 text-white">
            <h2 class="fw-bold mb-1"><i class="bi bi-building-fill text-info me-2"></i>Hotel Manager</h2>
            <p class="text-light opacity-75 small text-uppercase" style="letter-spacing: 1px;">Portail d'Authentification Personnel</p>
        </div>

        <div class="card login-card p-4">
            <div class="card-body">
                <h4 class="card-title fw-bold text-dark mb-4 text-center">Connexion</h4>

                <!-- Flash messages de succès -->
                @if(session('success'))
                    <div class="alert alert-success border-0 small py-2 rounded-3 text-center mb-3">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Affichage des erreurs de connexion -->
                @if($errors->any())
                    <div class="alert alert-danger border-0 small py-2 rounded-3 mb-3">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.process') }}" method="POST">
                    @csrf

                    <!-- Champ Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold text-secondary small text-uppercase">Adresse Email Professionnelle</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control border-start-0 ps-0 fw-semibold text-dark" id="email" name="email" value="{{ old('email') }}" placeholder="nom@hotel.com" required autofocus>
                        </div>
                    </div>

                    <!-- Champ Mot de passe avec bouton Œil -->

                    <!-- Champ Mot de passe avec bouton Œil -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="password" class="form-label fw-bold text-secondary small text-uppercase mb-1">Mot de passe</label>
                            <a href="#" class="text-decoration-none small fw-semibold text-primary" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                Mot de passe oublié ?
                            </a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 border-end-0 ps-0 text-dark" id="password" name="password" placeholder="••••••••" required>
                            <button class="input-group-text bg-light text-muted border-start-0 toggle-password" type="button" id="togglePasswordBtn" title="Afficher / Masquer le mot de passe">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>


                    <!-- Se souvenir de moi -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label text-muted small fw-semibold" for="remember">Rester connecté sur ce poste</label>
                    </div>

                    <!-- Bouton de validation -->
                    <button type="submit" class="btn btn-primary btn-login w-100 rounded-pill fw-bold py-2.5 shadow-sm text-uppercase">
                        <i class="bi bi-box-arrow-in-right me-2"></i>S'authentifier
                    </button>
                </form>

                <!-- Séparateur et Accès Portail Client -->
                <div class="mt-4 pt-3 border-top text-center">
                    <p class="text-muted small mb-2">Vous êtes client de l'établissement ?</p>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#guestPortalModal">
                        <i class="bi bi-qr-code-scan text-primary me-2"></i>Accès Portail Client & Room Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fenêtre modale Mot de passe oublié -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark"><i class="bi bi-shield-lock text-primary me-2"></i>Mot de passe oublié</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body small text-muted">
                    Pour des raisons de sécurité sur le réseau interne de l'hôtel, veuillez contacter l'<strong>Administrateur Système</strong> ou la <strong>Direction</strong> pour réinitialiser votre accès.
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 w-100" data-bs-dismiss="modal">Compris</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Fenêtre modale d'accès rapide au Portail Client par Chambre -->
    <div class="modal fade" id="guestPortalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold text-dark">
                        <i class="bi bi-qr-code-scan text-primary me-2"></i>Portail Client
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Scannez le <strong>QR Code</strong> présent dans votre chambre ou sélectionnez votre chambre ci-dessous :
                    </p>

                    <select id="guestRoomSelect" class="form-select form-select-sm fw-semibold mb-3">
                        <option value="">-- Choisir votre chambre --</option>
                        @forelse(\App\Models\Room::where('status', 'occupied')->orderBy('number')->get() as $r)
                            <option value="{{ Route::has('guest.portal') ? route('guest.portal', $r->id) : url('/room-service/chambre/' . $r->id) }}">
                                Chambre {{ $r->number }} ({{ strtoupper($r->type) }})
                            </option>
                        @empty
                            <option value="" disabled>Aucune chambre occupée actuellement</option>
                        @endforelse
                    </select>


                    <button type="button" id="goToGuestPortalBtn" class="btn btn-primary btn-sm rounded-pill px-3 w-100 fw-bold">
                        <i class="bi bi-door-open me-1"></i> Accéder au Room Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Afficher / Masquer le mot de passe
            const passwordInput = document.getElementById('password');
            const toggleBtn     = document.getElementById('togglePasswordBtn');
            const toggleIcon    = document.getElementById('togglePasswordIcon');

            if (passwordInput && toggleBtn && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    toggleIcon.classList.toggle('bi-eye', !isPassword);
                    toggleIcon.classList.toggle('bi-eye-slash', isPassword);
                });
            }

            // 2. Redirection vers le Portail Client de la chambre sélectionnée
            const goBtn      = document.getElementById('goToGuestPortalBtn');
            const roomSelect = document.getElementById('guestRoomSelect');

            if (goBtn && roomSelect) {
                goBtn.addEventListener('click', function () {
                    if (roomSelect.value) {
                        window.location.href = roomSelect.value;
                    } else {
                        roomSelect.focus();
                    }
                });
            }
        });
    </script>

</body>
</html>
