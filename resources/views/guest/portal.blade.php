<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Service & Conciergerie - Chambre {{ $room->number }}</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding-bottom: 90px;
        }
        .guest-header {
            background: linear-gradient(135deg, #1e1b4b 0%, #1e3a8a 100%);
            color: #fff;
            border-bottom-left-radius: 24px;
            border-bottom-right-radius: 24px;
            box-shadow: 0 6px 20px rgba(30, 27, 75, 0.25);
        }
        .nav-pills .nav-link {
            color: #475569;
            font-weight: 700;
            border-radius: 50px;
            padding: 10px 18px;
        }
        .nav-pills .nav-link.active {
            background-color: #2563eb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .menu-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: transform 0.15s;
        }
        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: bold;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .floating-cart {
            position: fixed;
            bottom: 16px;
            left: 16px;
            right: 16px;
            z-index: 1050;
            border-radius: 50px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.3);
        }
    </style>
</head>
<body>

    <!-- En-tête mobile de bienvenue -->
    <div class="guest-header p-4 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold">
                <i class="bi bi-door-open-fill me-1"></i> {{ $room->number }}
            </span>
            <span class="small opacity-75">Hotel Manager Resort</span>
        </div>
        <h4 class="fw-bold mb-1">Bienvenue dans votre espace</h4>
        <p class="small opacity-90 mb-0">Commandez votre repas en chambre ou sollicitez notre équipe d'étage en un clic.</p>
    </div>

    <div class="container" style="max-width: 600px;">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 small mb-3">
                <i class="bi bi-check-circle-fill me-1"></i> <strong>{{ session('success') }}</strong>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 small mb-3">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <strong>{{ session('error') }}</strong>
            </div>
        @endif

        <!-- Onglets de navigation mobile -->
        <ul class="nav nav-pills nav-fill bg-white p-1 rounded-pill shadow-sm mb-4" id="guestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="resto-tab" data-bs-toggle="pill" data-bs-target="#restoPane" type="button">
                    <i class="bi bi-cup-hot-fill me-1"></i> Room Service
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="service-tab" data-bs-toggle="pill" data-bs-target="#servicePane" type="button">
                    <i class="bi bi-bell-fill me-1"></i> Gouvernance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="suivi-tab" data-bs-toggle="pill" data-bs-target="#suiviPane" type="button">
                    <i class="bi bi-clock-history me-1"></i> Suivi
                </button>
            </li>
        </ul>

        <div class="tab-content" id="guestTabsContent">
            <!-- ONGLET 1 : CARTE RESTAURANT & BAR (ROOM SERVICE) -->
            <div class="tab-pane fade show active" id="restoPane" role="tabpanel">
                <form action="{{ route('guest.order', $room->id) }}" method="POST" id="roomServiceForm">
                    @csrf
                    @php
                        $groupedMenu = $menuItems->groupBy('category');
                    @endphp

                    @forelse($groupedMenu as $category => $items)
                        <h6 class="fw-bold text-uppercase text-primary small mt-3 mb-2 px-1">
                            {{ $category ?: 'Carte & Boissons' }}
                        </h6>

                        @foreach($items as $item)
                            <div class="card menu-card bg-white p-3 mb-2 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="pe-2">
                                        <div class="fw-bold text-dark">{{ $item->name }}</div>
                                        @if(!empty($item->description))
                                            <div class="text-muted" style="font-size: 0.78rem;">{{ $item->description }}</div>
                                        @endif
                                        <div class="fw-bold text-success small mt-1">
                                            {{ number_format($item->price, 0, ',', ' ') }} F CFA
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-outline-secondary qty-btn btn-minus" data-id="{{ $item->id }}">-</button>
                                        <input type="number"
                                               name="quantities[{{ $item->id }}]"
                                               id="qty-{{ $item->id }}"
                                               value="0"
                                               min="0"
                                               readonly
                                               data-price="{{ $item->price }}"
                                               class="form-control form-control-sm text-center fw-bold border-0 bg-light qty-input"
                                               style="width: 38px;">
                                        <button type="button" class="btn btn-primary qty-btn btn-plus" data-id="{{ $item->id }}">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center py-5 text-muted small">
                            La carte du restaurant est momentanément indisponible.
                        </div>
                    @endforelse

                    <!-- Barre flottante de validation de commande -->
                    <div id="floatingOrderBar" class="floating-cart bg-dark text-white px-4 py-3 d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-warning fw-bold" id="cartCountLabel">0 article(s)</div>
                                <div class="fw-bold fs-5" id="cartTotalLabel">0 F CFA</div>
                            </div>
                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                Commander <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ONGLET 2 : DEMANDES GOUVERNANCE & RÉCEPTION -->
            <div class="tab-pane fade" id="servicePane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h6 class="fw-bold text-dark mb-3">
                        <i class="bi bi-stars text-warning me-1"></i> Demande d'assistance en chambre
                    </h6>

                    <form action="{{ route('guest.request', $room->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">De quoi avez-vous besoin ? *</label>
                            <select name="type" class="form-select fw-semibold" required>
                                <option value="serviettes">🧺 Serviettes / Linge supplémentaire</option>
                                <option value="menage">🧹 Faire le ménage de la chambre</option>
                                <option value="eau_courtoisie">💧 Eau minérale / Kit courtoisie</option>
                                <option value="maintenance">🛠️ Signaler une panne (Clim, TV, Wi-Fi...)</option>
                                <option value="reveil">⏰ Demande de réveil / Taxi / Réception</option>
                                <option value="autre">💬 Autre demande</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small">Précisions (Optionnel)</label>
                            <textarea name="details" rows="3" class="form-control" placeholder="Ex: Merci d'apporter 2 grandes serviettes ou vérifier la climatisation..."></textarea>
                        </div>
                                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                            <i class="bi bi-send-fill me-1"></i> Envoyer à la Réception
                        </button>
                    </form>
                </div>
            </div>

            <!-- ONGLET 3 : SUIVI DES COMMANDES & DEMANDES -->
            <div class="tab-pane fade" id="suiviPane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-3">
                    <h6 class="fw-bold small text-uppercase text-primary mb-2">Mes Commandes Room Service</h6>
                    @forelse($myOrders as $ord)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                            <div>
                                <div class="fw-bold">Commande #{{ $ord->id }}</div>
                                <div class="text-muted">{{ \Carbon\Carbon::parse($ord->created_at)->format('d/m H:i') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">{{ number_format($ord->total_amount, 0, ',', ' ') }} F</div>
                                <span class="badge {{ $ord->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $ord->status === 'paid' ? 'Réglée' : 'Sur note chambre' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Aucune commande Room Service durant ce séjour.</p>
                    @endforelse
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <h6 class="fw-bold small text-uppercase text-success mb-2">Mes Demandes Étage / Réception</h6>
                    @forelse($myRequests as $req)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                            <div>
                                <div class="fw-bold text-uppercase">{{ str_replace('_', ' ', $req->type) }}</div>
                                <div class="text-muted">{{ $req->details ?: 'Demande standard' }}</div>
                            </div>
                            <span class="badge {{ $req->status === 'completed' ? 'bg-success' : 'bg-info text-dark' }}">
                                {{ $req->status === 'completed' ? 'Traitée' : 'En cours' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Aucune demande envoyée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://jsdelivr.net"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const floatingBar = document.getElementById('floatingOrderBar');
            const countLabel  = document.getElementById('cartCountLabel');
            const totalLabel  = document.getElementById('cartTotalLabel');

            function updateCart() {
                let totalQty = 0;
                let totalAmount = 0;

                document.querySelectorAll('.qty-input').forEach(input => {
                    const q = parseInt(input.value) || 0;
                    const p = parseFloat(input.getAttribute('data-price')) || 0;
                    totalQty += q;
                    totalAmount += (q * p);
                });

                if (totalQty > 0) {
                    floatingBar.classList.remove('d-none');
                    countLabel.textContent = totalQty + ' article(s) sélectionné(s)';
                    totalLabel.textContent = new Intl.NumberFormat('fr-FR').format(totalAmount) + ' F CFA';
                } else {
                    floatingBar.classList.add('d-none');
                }
            }

            document.querySelectorAll('.btn-plus').forEach(btn => {
                btn.addEventListener('click', function () {
                    const input = document.getElementById('qty-' + this.getAttribute('data-id'));
                    input.value = (parseInt(input.value) || 0) + 1;
                    updateCart();
                });
            });

            document.querySelectorAll('.btn-minus').forEach(btn => {
                btn.addEventListener('click', function () {
                    const input = document.getElementById('qty-' + this.getAttribute('data-id'));
                    const current = parseInt(input.value) || 0;
                    if (current > 0) {
                        input.value = current - 1;
                        updateCart();
                    }
                });
            });
        });
    </script>
</body>
</html>


