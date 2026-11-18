<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Service & Conciergerie - {{ $room->number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding-bottom: 105px;
            color: #0f172a;
        }

        /* En-tête Luxe Dégradé Bleu Nuit & Or */
        .hero-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 55%, #1e3a8a 100%);
            color: #ffffff;
            border-bottom-left-radius: 30px;
            border-bottom-right-radius: 30px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }
        .hero-header::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(250, 204, 21, 0.12);
        }

        /* Barre d'onglets flottante */
        .tab-switcher {
            background: #ffffff;
            border-radius: 50px;
            padding: 5px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
            margin-top: -26px;
            position: relative;
            z-index: 10;
        }
        .tab-switcher .nav-link {
            border-radius: 50px;
            color: #64748b;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 12px;
            transition: all 0.2s ease;
        }
        .tab-switcher .nav-link.active {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        /* Pastilles de filtres rapides */
        .filter-pill {
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 6px 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            transition: all 0.15s;
        }
        .filter-pill.active, .filter-pill:hover {
            background: #0f172a;
            color: #facc15;
            border-color: #0f172a;
        }

        /* Cartes Plats & Boissons */
        .dish-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            transition: transform 0.15s, border-color 0.2s;
        }
        .dish-card.selected-dish {
            border: 2px solid #2563eb;
            background: #eff6ff;
        }
        .dish-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .qty-control {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 50px;
            padding: 3px;
        }
        .qty-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: none;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.1s;
        }
        .qty-btn:active {
            transform: scale(0.9);
        }

        /* Boutons rapides Gouvernance */
        .quick-service-card {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quick-service-card:hover, .quick-service-card.active-service {
            border-color: #2563eb;
            background: #eff6ff;
            transform: translateY(-2px);
        }

        /* Panier Flottant Vitré */
        .floating-checkout {
            position: fixed;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 32px);
            max-width: 560px;
            background: rgba(15, 23, 42, 0.96);
            backdrop-filter: blur(10px);
            color: #ffffff;
            border-radius: 24px;
            padding: 14px 20px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.35);
            z-index: 1050;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    </style>
</head>
<body>

    <!-- EN-TÊTE LUXE -->
    <div class="hero-header px-4 pt-4 pb-5">
        <div class="container" style="max-width: 580px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold shadow-sm">
                    <i class="bi bi-door-open-fill me-1"></i> {{ $room->number }}
                </span>
                <span class="small fw-semibold text-uppercase tracking-wider text-info">
                    <i class="bi bi-stars me-1"></i> Hotel Manager Resort
                </span>
            </div>
            <h3 class="fw-bold mb-1">Conciergerie & Room Service</h3>
            <p class="small text-white-50 mb-0">
                Commandez votre repas en chambre ou sollicitez notre équipe d'étage 24h/24.
            </p>
        </div>
    </div>

    <div class="container" style="max-width: 580px;">
        <!-- NAVIGATION PAR ONGLETS FLOTTANTE -->
        <ul class="nav nav-pills nav-fill tab-switcher mb-4" id="guestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="resto-tab" data-bs-toggle="pill" data-bs-target="#restoPane" type="button">
                    <i class="bi bi-cup-hot-fill me-1"></i> Carte & Bar
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="service-tab" data-bs-toggle="pill" data-bs-target="#servicePane" type="button">
                    <i class="bi bi-bell-fill me-1"></i> Gouvernance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="suivi-tab" data-bs-toggle="pill" data-bs-target="#suiviPane" type="button">
                    <i class="bi bi-receipt me-1"></i> Suivi
                    @if(($myOrders->count() + $myRequests->count()) > 0)
                        <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $myOrders->count() + $myRequests->count() }}</span>
                    @endif
                </button>
            </li>
        </ul>

        <!-- MESSAGES DE CONFIRMATION -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 small d-flex align-items-center mb-3">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div><strong>{{ session('success') }}</strong></div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 small d-flex align-items-center mb-3">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div><strong>{{ session('error') }}</strong></div>
            </div>
        @endif

        <div class="tab-content" id="guestTabsContent">
            <!-- ============================================================ -->
            <!-- ONGLET 1 : CARTE RESTAURANT & BAR                            -->
            <!-- ============================================================ -->
            <div class="tab-pane fade show active" id="restoPane" role="tabpanel">
                <!-- Recherche & Filtres rapides -->
                <div class="mb-3">
                    <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden bg-white border mb-2">
                        <span class="input-group-text bg-white border-0 ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="menuSearchInput" class="form-control border-0 py-2" placeholder="Rechercher un plat, une boisson...">
                    </div>
                    <div class="d-flex gap-2 overflow-auto pb-1">
                        <button type="button" class="filter-pill active" data-filter="all">Tout voir</button>
                        <button type="button" class="filter-pill" data-filter="plat">🍽️ Plats & Grillades</button>
                        <button type="button" class="filter-pill" data-filter="boisson">🍹 Boissons & Vins</button>
                    </div>
                </div>

                <form action="{{ route('guest.order', $room->id) }}" method="POST" id="roomServiceForm">
                    @csrf
                    <div id="menuListContainer">
                        @forelse($menuItems as $item)
                            @php
                                $lowerName = mb_strtolower($item->name);
                                $isDrink = str_contains($lowerName, 'coca') || str_contains($lowerName, 'cocktail') || str_contains($lowerName, 'vin') || str_contains($lowerName, 'eau') || str_contains($lowerName, 'jus') || str_contains($lowerName, 'bière');
                                $itemType = $isDrink ? 'boisson' : 'plat';
                                $iconBg = $isDrink ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning';
                                $iconEmoji = $isDrink ? '🍹' : '🍽️';
                            @endphp

                            <div class="dish-card p-3 mb-3 menu-item-row"
                                 id="card-{{ $item->id }}"
                                 data-name="{{ $lowerName }}"
                                 data-type="{{ $itemType }}">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="dish-icon {{ $iconBg }}">
                                            {{ $iconEmoji }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6">{{ $item->name }}</div>
                                            @if(!empty($item->description))
                                                <div class="text-muted lh-sm mt-1" style="font-size: 0.78rem;">
                                                    {{ $item->description }}
                                                </div>
                                            @endif
                                            <div class="fw-extrabold text-primary mt-1" style="font-size: 0.92rem;">
                                                {{ number_format($item->price, 0, ',', ' ') }} F CFA
                                            </div>
                                        </div>
                                    </div>

                                    <div class="qty-control d-flex align-items-center gap-1">
                                        <button type="button" class="qty-btn bg-white text-dark shadow-sm btn-minus" data-id="{{ $item->id }}">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <input type="number"
                                               name="quantities[{{ $item->id }}]"
                                               id="qty-{{ $item->id }}"
                                               value="0"
                                               min="0"
                                               readonly
                                               data-price="{{ $item->price }}"
                                               class="form-control form-control-sm text-center fw-bold border-0 bg-transparent p-0 qty-input"
                                               style="width: 28px;">
                                        <button type="button" class="qty-btn bg-primary text-white shadow-sm btn-plus" data-id="{{ $item->id }}">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted small">
                                La carte du restaurant est momentanément indisponible.
                            </div>
                        @endforelse
                    </div>

                    <!-- Barre flottante vitrée de commande (masquée si panier vide) -->
                    <div id="floatingOrderBar" class="floating-checkout d-none">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="small text-warning fw-bold" id="cartCountLabel">0 article</div>
                                <div class="fw-bold fs-4 lh-1 mt-1" id="cartTotalLabel">0 F CFA</div>
                            </div>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold shadow">
                                Commander <i class="bi bi-bag-check-fill ms-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- ============================================================ -->
            <!-- ONGLET 2 : DEMANDES GOUVERNANCE & RÉCEPTION (TUILES RAPIDES)  -->
            <!-- ============================================================ -->
            <div class="tab-pane fade" id="servicePane" role="tabpanel">
                <form action="{{ route('guest.request', $room->id) }}" method="POST" id="conciergeForm">
                    @csrf
                    <input type="hidden" name="type" id="selectedServiceType" value="serviettes">

                    <h6 class="fw-bold text-dark mb-3 px-1">Sélectionnez un service rapide :</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="quick-service-card active-service" data-service="serviettes">
                                <div class="fs-2 mb-1">🧺</div>
                                <div class="fw-bold small">Serviettes</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="quick-service-card" data-service="menage">
                                <div class="fs-2 mb-1">🧹</div>
                                <div class="fw-bold small">Ménage</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="quick-service-card" data-service="eau_courtoisie">
                                <div class="fs-2 mb-1">💧</div>
                                <div class="fw-bold small">Eau / Kit</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="quick-service-card" data-service="maintenance">
                                <div class="fs-2 mb-1">🛠️</div>
                                <div class="fw-bold small">Clim / Wi-Fi</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="quick-service-card" data-service="reveil">
                                <div class="fs-2 mb-1">🚕</div>
                                <div class="fw-bold small">Taxi / Réveil</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="quick-service-card" data-service="autre">
                                <div class="fs-2 mb-1">💬</div>
                                <div class="fw-bold small">Autre</div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Précisions pour notre équipe (Optionnel)</label>
                            <textarea name="details" rows="3" class="form-control rounded-3" placeholder="Ex: Merci d'apporter 2 grandes serviettes, ou réveil à 06h30..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow">
                            <i class="bi bi-send-fill me-1"></i> Envoyer la demande à l'étage
                        </button>
                    </div>
                </form>
            </div>

            <!-- ============================================================ -->
            <!-- ONGLET 3 : SUIVI DU SÉJOUR EN COURS                          -->
            <!-- ============================================================ -->
            <div class="tab-pane fade" id="suiviPane" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-3">
                    <h6 class="fw-bold small text-uppercase text-primary mb-3">
                        <i class="bi bi-bag-check-fill me-1"></i> Mes Commandes Room Service
                    </h6>
                    @forelse($myOrders as $ord)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                            <div>
                                <div class="fw-bold text-dark">Commande #{{ $ord->id }}</div>
                                <div class="text-muted">{{ \Carbon\Carbon::parse($ord->created_at)->format('d/m à H:i') }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark">{{ number_format($ord->total_amount, 0, ',', ' ') }} F</div>
                                <span class="badge rounded-pill {{ $ord->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $ord->status === 'paid' ? 'Réglée' : 'Sur note chambre' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 text-center py-2">Aucune commande Room Service passée.</p>
                    @endforelse
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <h6 class="fw-bold small text-uppercase text-success mb-3">
                        <i class="bi bi-bell-fill me-1"></i> Mes Demandes Étage & Réception
                    </h6>
                    @forelse($myRequests as $req)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                            <div>
                                <div class="fw-bold text-uppercase text-dark">{{ str_replace('_', ' ', $req->type) }}</div>
                                <div class="text-muted">{{ $req->details ?: 'Demande standard' }}</div>
                            </div>
                            <span class="badge rounded-pill {{ $req->status === 'completed' ? 'bg-success' : 'bg-info text-dark' }}">
                                {{ $req->status === 'completed' ? 'Traitée' : 'En cours' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 text-center py-2">Aucune demande d'assistance envoyée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const floatingBar = document.getElementById('floatingOrderBar');
            const countLabel  = document.getElementById('cartCountLabel');
            const totalLabel  = document.getElementById('cartTotalLabel');

            // 1. Calcul dynamique du panier & surbrillance de la carte sélectionnée
            function updateCart() {
                let totalQty = 0;
                let totalAmount = 0;

                document.querySelectorAll('.qty-input').forEach(input => {
                    const id = input.id.replace('qty-', '');
                    const card = document.getElementById('card-' + id);
                    const q = parseInt(input.value) || 0;
                    const p = parseFloat(input.getAttribute('data-price')) || 0;

                    if (q > 0) {
                        card.classList.add('selected-dish');
                    } else {
                        card.classList.remove('selected-dish');
                    }

                    totalQty += q;
                    totalAmount += (q * p);
                });

                if (totalQty > 0) {
                    floatingBar.classList.remove('d-none');
                    countLabel.textContent = totalQty + ' article' + (totalQty > 1 ? 's' : '') + ' • Note Chambre';
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

            // 2. Recherche et filtres instantanés (Tout / Plats / Boissons)
            const searchInput = document.getElementById('menuSearchInput');
            let currentFilter = 'all';

            function filterMenu() {
                const term = searchInput.value.toLowerCase().trim();
                document.querySelectorAll('.menu-item-row').forEach(row => {
                    const name = row.getAttribute('data-name');
                    const type = row.getAttribute('data-type');
                    const matchesSearch = name.includes(term);
                    const matchesType = (currentFilter === 'all' || type === currentFilter);
                    row.style.display = (matchesSearch && matchesType) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterMenu);

            document.querySelectorAll('.filter-pill').forEach(pill => {
                pill.addEventListener('click', function () {
                    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = this.getAttribute('data-filter');
                    filterMenu();
                });
            });

            // 3. Sélection en 1 clic des tuiles Gouvernance
            document.querySelectorAll('.quick-service-card').forEach(card => {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.quick-service-card').forEach(c => c.classList.remove('active-service'));
                    this.classList.add('active-service');
                    document.getElementById('selectedServiceType').value = this.getAttribute('data-service');
                });
            });
        });
    </script>
</body>
</html>
