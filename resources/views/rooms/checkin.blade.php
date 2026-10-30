@extends('layouts.app')

@section('title', 'Attribution Carte RFID - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-card-list me-2"></i>Check-in : Attribution de Carte Magnétique</h1>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Enregistrer un nouveau client</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('checkin.save') }}" method="POST">
                    @csrf

                    <!-- Sélection de la chambre -->
                    <div class="mb-3">
                        <label for="room_id" class="form-label fw-bold">Sélectionner la Chambre disponible</label>
                        <select class="form-select @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                            <option value="" selected disabled>Choisir une chambre...</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">{{ $room->number }} ({{ $room->type }} - {{ number_format($room->price_per_night, 0, ',', ' ') }} F/nuit)</option>
                            @endforeach
                        </select>
                        @error('room_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Code RFID de la carte -->
                    <!-- Code RFID de la carte avec Générateur Virtuel -->
                    <div class="mb-3">
                        <label for_id="card_uid" class="form-label fw-bold">Numéro Physique de la Carte (UID RFID)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-upc-scan"></i></span>
                            <input type="text" class="form-control @error('card_uid') is-invalid @enderror" id="card_uid" name="card_uid" placeholder="Ex: RFID_88Z99K" required>
                            <!-- Bouton de simulation mécanique -->
                            <button class="btn btn-outline-secondary" type="button" id="btn-simulate-card" onclick="simulateCardScan()">
                                <i class="bi bi-cpu-fill me-1"></i> Simuler un Badge
                            </button>
                        </div>
                        <small class="form-text text-muted">En développement : cliquez sur "Simuler un Badge" pour générer un code virtuel sans lecteur physique.</small>
                        @error('card_uid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <script>
                        // Fonction JavaScript pour simuler le passage d'une carte RFID
                        function simulateCardScan() {
                            // Génère une chaîne hexadécimale aléatoire de 8 caractères (ex: 4F89BC2A)
                            const characters = '0123456789ABCDEF';
                            let mockUid = 'RFID_';
                            for (let i = 0; i < 8; i++) {
                                mockUid += characters.charAt(Math.floor(Math.random() * characters.length));
                            }
                            // Injecte le faux code dans le champ de saisie
                            document.getElementById('card_uid').value = mockUid;
                        }
                    </script>


                    <!-- Durée du séjour -->
                    <div class="mb-3">
                        <label for="days" class="form-label fw-bold">Durée du séjour (Nuits)</label>
                        <input type="number" class="form-control @error('days') is-invalid @enderror" id="days" name="days" min="1" value="1" required>
                        @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check2-square me-2"></i>Valider et Activer la Carte</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="alert alert-info shadow-sm">
            <h5><i class="bi bi-info-circle-fill me-2"></i>Fonctionnement du suivi à distance</h5>
            <p class="mb-0">Dès que vous cliquez sur "Valider", le système effectue trois actions instantanées :</p>
            <ol class="mt-2 mb-0">
                <li>La chambre change d'état et passe à <strong>"Occupée"</strong>.</li>
                <li>Un jeton de cryptage unique est associé à la carte pour sécuriser les requêtes HTTP.</li>
                <li>Toutes les serrures réseau connectées mettent à jour leurs autorisations d'accès à distance.</li>
            </ol>
        </div>
    </div>
</div>
@endsection
