@extends('layouts.app')

@section('title', 'Gestion de la Galerie & Album - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h2 text-dark mb-0"><i class="bi bi-images text-primary me-2"></i>Galerie & Album Photos de l'Hôtel</h1>
        <small class="text-muted">Gérez les photos affichées sur la page publique de réservation en ligne</small>
    </div>
    <a href="{{ route('public.booking.index') }}" target="_blank" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
        <i class="bi bi-eye me-1"></i> Voir le rendu public
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- Formulaire d'ajout à gauche -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white fw-bold py-3 px-4">
                <i class="bi bi-cloud-arrow-up-fill me-2"></i>Ajouter une Photo à l'Album
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Catégorie d'espace *</label>
                        <select name="category" class="form-select fw-semibold" required>
                            <option value="restaurant">🍽️ Restaurant & Bar</option>
                            <option value="piscine">🏊 Piscine & Détente</option>
                            <option value="salles">🎤 Salles Pro (Séminaire / Coworking)</option>
                            <option value="spectacle">🎟️ Salle de Spectacle & Concerts</option>
                            <option value="chambres">🛏️ Chambres & Suites</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Titre affiché *</label>
                        <input type="text" name="title" class="form-control" placeholder="Ex: Terrasse Piscine Nocturne" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Courte description</label>
                        <input type="text" name="description" class="form-control" placeholder="Ex: Espace détente ouvert de 07h à 22h">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Fichier Photo *</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text small">Optimisation et compression automatiques (JPG, PNG, WebP).</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Publier dans l'Album
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Liste des photos existantes à droite -->
    <div class="col-lg-8">
        <div class="row g-3">
            @forelse($items as $item)
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="position-relative" style="height: 170px;">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-100 h-100" style="object-fit: cover;">
                            <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-2 rounded-pill px-3">
                                {{ strtoupper($item->category) }}
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('admin.gallery.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <select name="category" class="form-select form-select-sm fw-semibold mb-2">
                                        <option value="restaurant" {{ $item->category === 'restaurant' ? 'selected' : '' }}>🍽️ Restaurant</option>
                                        <option value="piscine" {{ $item->category === 'piscine' ? 'selected' : '' }}>🏊 Piscine</option>
                                        <option value="salles" {{ $item->category === 'salles' ? 'selected' : '' }}>🎤 Salles Pro</option>
                                        <option value="spectacle" {{ $item->category === 'spectacle' ? 'selected' : '' }}>🎟️ Spectacle</option>
                                        <option value="chambres" {{ $item->category === 'chambres' ? 'selected' : '' }}>🛏️ Chambres</option>
                                    </select>
                                    <input type="text" name="title" value="{{ $item->title }}" class="form-control form-control-sm fw-bold mb-2" required>
                                    <input type="text" name="description" value="{{ $item->description }}" class="form-control form-control-sm text-muted mb-2" placeholder="Description">
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*" title="Remplacer la photo">
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-check-lg me-1"></i> Enregistrer
                                    </button>
                            </form>
                                    <form action="{{ route('admin.gallery.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Supprimer cette photo de l\'album ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info border-0 rounded-4">Aucune photo enregistrée dans l'album.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
