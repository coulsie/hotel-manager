<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Service Inactif - {{ $room->number }}</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <link href="https://jsdelivr.net" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container text-center px-4" style="max-width: 440px;">
        <div class="card border-0 shadow rounded-4 p-4">
            <div class="mb-3">
                <span class="bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="bi bi-door-closed-fill fs-1"></i>
                </span>
            </div>
            <h4 class="fw-bold text-dark mb-2">{{ $room->number }}</h4>
            <p class="text-muted small mb-3">
                Le service de commande en ligne et conciergerie numérique de cette chambre n'est pas encore activé.
            </p>
            <div class="alert alert-info border-0 small rounded-3 mb-0">
                <i class="bi bi-info-circle-fill me-1"></i>
                Si vous venez d'arriver, veuillez contacter la <strong>Réception</strong> afin d'activer votre séjour.
            </div>
        </div>
    </div>
</body>
</html>
