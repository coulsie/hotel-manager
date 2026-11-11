<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Hotel Manager</title>
    <!-- Chargement de Bootstrap via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Icônes Bootstrap -->
   

    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
        }
        .btn-login {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            transition: all 0.2s;
        }
        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container" style="max-width: 450px;">

        <!-- Logo ou Titre de l'établissement -->
        <div class="text-center mb-4 text-white">
            <h2 class="fw-bold mb-1"><i class="bi bi-building-fill text-info me-2"></i>Hotel Manager</h2>
            <p class="text-muted small text-uppercase tracking-wider">Portail d'Authentification Personnel</p>
        </div>

        <div class="card login-card p-4">
            <div class="card-body">
                <h4 class="card-title fw-bold text-dark mb-4 text-center">Connexion</h4>

                <!-- Flash messages de succès (ex: après déconnexion) -->
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

                    <!-- Champ Mot de passe -->
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control border-start-0 ps-0 text-dark" id="password" name="password" placeholder="••••••••" required>
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
            </div>
        </div>
    </div>

</body>
</html>
