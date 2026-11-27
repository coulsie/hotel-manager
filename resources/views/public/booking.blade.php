<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation en Ligne - Hotel Manager Resort</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://jsdelivr.net">
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Roboto, sans-serif; }
        .hero-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            color: #ffffff;
            padding: 3.5rem 0 5rem 0;
        }
        .search-box {
            margin-top: -3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }
        .room-card {
            border-radius: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .room-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>

    <!-- En-tête Vitrine -->
    <div class="hero-banner">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="fw-bold fs-4"><i class="bi bi-building-fill text-warning me-2"></i>HOTEL MANAGER RESORT</span>
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                    <i class="bi bi-person-lock me-1"></i> Espace Personnel
                </a>
            </div>
            <div class="text-center max-w-2xl mx-auto">
                <h1 class="fw-bold display-6 mb-2">Réservez votre séjour en quelques clics</h1>
                <p class="opacity-75 mb-0">Abidjan, Côte d'Ivoire • Confirmation immédiate et paiement à l'arrivée ou par Mobile Money</p>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Moteur de recherche de disponibilité -->
        <!-- Moteur de recherche de disponibilité -->
        <div class="card border-0 bg-white search-box p-4 mb-5">
            <form action="{{ route('public.booking.index') }}#resultats" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Date d'arrivée</label>
                    <input type="date" id="checkInInput" name="check_in_date" class="form-control fw-semibold" min="{{ date('Y-m-d') }}" value="{{ $checkIn }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Date de départ</label>
                    <input type="date" id="checkOutInput" name="check_out_date" class="form-control fw-semibold" min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ $checkOut }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small text-uppercase text-secondary">Standing souhaité</label>
                    <select name="type" class="form-select fw-semibold">
                        <option value="">Toutes les catégories</option>
                        <option value="Standard" {{ request('type') === 'Standard' ? 'selected' : '' }}>🛏️ Standard</option>
                        <option value="Double" {{ request('type') === 'Double' ? 'selected' : '' }}>👥 Double / Twin</option>
                        <option value="Luxe" {{ request('type') === 'Luxe' ? 'selected' : '' }}>✨ Luxe</option>
                        <option value="Suite Junior" {{ request('type') === 'Suite Junior' ? 'selected' : '' }}>👑 Suite Junior</option>
                        <option value="Suite VIP" {{ request('type') === 'Suite VIP' ? 'selected' : '' }}>💎 Suite VIP</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow-sm">
                        <i class="bi bi-search me-2"></i>Vérifier ({{ $nights }} nuit{{ $nights > 1 ? 's' : '' }})
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirmation de réservation réussie -->
        @if(session('booking_confirmed'))
            @php $conf = session('booking_confirmed'); @endphp
            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-5">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-check-circle-fill fs-3 text-success me-3"></i>
                    <div>
                        <h5 class="fw-bold mb-0">Réservation Confirmée ! Réf : {{ $conf['reference'] }}</h5>
                        <small class="text-muted">Merci {{ $conf['client'] }}, votre chambre est bloquée dans notre planning.</small>
                    </div>
                </div>
                <hr>
                <div class="row small fw-semibold">
                    <div class="col-sm-4">Chambre : <span class="text-dark">{{ $conf['room'] }}</span></div>
                    <div class="col-sm-4">Séjour : <span class="text-dark">Du {{ $conf['check_in'] }} au {{ $conf['check_out'] }} ({{ $conf['nights'] }} nuit(s))</span></div>
                    <div class="col-sm-4 text-sm-end">Total à régler : <span class="text-success fs-6">{{ $conf['total'] }}</span></div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Liste des chambres disponibles -->
        <div id="resultats" class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pt-2">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-door-open text-primary me-2"></i>Chambres disponibles du {{ \Carbon\Carbon::parse($checkIn)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }}
            </h4>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fs-6">
                {{ $availableRooms->count() }} chambre(s) libre(s) • {{ $nights }} nuit{{ $nights > 1 ? 's' : '' }}
            </span>
        </div>


               <div class="row g-4">
            @forelse($availableRooms as $room)
                @php
                    $stayTotal = (float) $room->price_per_night * $nights;
                    $roomImageUrl = $room->image
                        ? asset('storage/' . ltrim($room->image, '/'))
                        : null;
                @endphp

                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 room-card overflow-hidden">
                        <!-- Image de la chambre -->
                        <div class="position-relative bg-dark" style="height: 200px; overflow: hidden;">
                            @if($roomImageUrl)
                                <img src="{{ $roomImageUrl }}"
                                        alt="{{ $room->number }}"
                                        class="w-100 h-100"
                                        loading="lazy"
                                        decoding="async"
                                        style="object-fit: cover;">

                            @endif
                            <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow">
                                {{ $room->number }}
                            </span>
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill fw-bold shadow">
                                {{ strtoupper($room->type) }}
                            </span>
                        </div>

                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-baseline mb-3">
                                    <span class="text-muted small">Tarif / nuit</span>
                                    <span class="fw-bold text-dark fs-5">{{ number_format($room->price_per_night, 0, ',', ' ') }} F</span>
                                </div>
                                <div class="p-3 bg-light rounded-3 mb-3 d-flex justify-content-between align-items-center">
                                    <span class="small fw-semibold text-secondary">Total ({{ $nights }} nuit{{ $nights > 1 ? 's' : '' }})</span>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($stayTotal, 0, ',', ' ') }} F</span>
                                </div>
                                <ul class="list-unstyled small text-muted mb-4">
                                    <li class="mb-1"><i class="bi bi-wifi text-success me-2"></i>Wi-Fi Haut Débit gratuit</li>
                                    <li class="mb-1"><i class="bi bi-snow text-info me-2"></i>Climatisation & TV écran plat</li>
                                    <li><i class="bi bi-shield-check text-primary me-2"></i>Accès carte magnétique sécurisé</li>
                                </ul>
                            </div>

                            <button type="button"
                                    class="btn btn-primary w-100 rounded-pill fw-bold py-2 select-room-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#bookingModal"
                                    data-room-id="{{ $room->id }}"
                                    data-room-label="{{ $room->number }} ({{ strtoupper($room->type) }})"
                                    data-stay-total="{{ number_format($stayTotal, 0, ',', ' ') }} F CFA">
                                Réserver cette chambre
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning border-0 rounded-4 p-4 text-center">
                        <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                        Aucune chambre n'est disponible sur ces dates. Essayez de modifier votre période de séjour.
                    </div>
                </div>
            @endforelse
        </div>

    </div>

    <!-- Modale de finalisation de réservation -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('public.booking.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" id="modalRoomId">
                    <input type="hidden" name="check_in_date" value="{{ $checkIn }}">
                    <input type="hidden" name="check_out_date" value="{{ $checkOut }}">

                    <div class="modal-header bg-light border-0">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="bi bi-calendar2-check text-primary me-2"></i>Finaliser ma réservation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="p-3 bg-primary-subtle rounded-3 mb-3 small">
                            <div class="fw-bold text-primary" id="modalRoomTitle"></div>
                            <div class="text-dark">Du {{ \Carbon\Carbon::parse($checkIn)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }} ({{ $nights }} nuit(s))</div>
                            <div class="fw-bold text-dark mt-1">Montant Total : <span id="modalStayTotal"></span></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nom & Prénoms *</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Ex: Kouassi Jean-Marc" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Téléphone / WhatsApp *</label>
                                <input type="text" name="phone" class="form-control" placeholder="+225 07 00 00 00 00" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="client@email.com">
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Ville / Pays de provenance</label>
                                <input type="text" name="provenance" class="form-control" placeholder="Ex: Bouaké / Paris">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Destination</label>
                                <input type="text" name="destination" class="form-control" placeholder="Ex: Abidjan">
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold">Demandes particulières (Optionnel)</label>
                            <textarea name="special_requests" class="form-control" rows="2" placeholder="Heure d'arrivée estimée, lit bébé, transfert aéroport..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Confirmer la réservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>

            document.addEventListener('DOMContentLoaded', function () {
                // 1. Remplissage dynamique de la modale lors du clic sur "Réserver cette chambre"
                document.querySelectorAll('.select-room-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        document.getElementById('modalRoomId').value = this.dataset.roomId;
                        document.getElementById('modalRoomTitle').textContent = this.dataset.roomLabel;
                        document.getElementById('modalStayTotal').textContent = this.dataset.stayTotal;
                    });
                });

                // 2. Mise à jour automatique de la date de départ minimum (+1 jour)
                const checkInInput  = document.getElementById('checkInInput');
                const checkOutInput = document.getElementById('checkOutInput');

                if (checkInInput && checkOutInput) {
                    checkInInput.addEventListener('change', function () {
                        if (!this.value) return;
                        const nextDay = new Date(this.value);
                        nextDay.setDate(nextDay.getDate() + 1);
                        const minOut = nextDay.toISOString().split('T');
                        checkOutInput.min = minOut;
                        if (checkOutInput.value <= this.value) {
                            checkOutInput.value = minOut;
                        }
                    });
                }
            });

    </script>
</body>
</html>
