@extends('layouts.app')
@section('content')
<div class="pt-3 pb-2 mb-3 border-bottom"><h2><i class="bi bi-plus-circle me-2"></i>Ajouter une Chambre</h2></div>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<div class="card shadow-sm col-md-6"><div class="card-body">
    <form action="{{ route('admin.rooms.store') }}" method="POST">
        @csrf
        <div class="mb-3"><label class="form-label fw-bold">Numéro de chambre</label><input type="text" name="number" class="form-control" placeholder="Ex: Chambre 205" required></div>
        <div class="mb-3"><label class="form-label fw-bold">Type</label><select name="type" class="form-select" required><option value="Standard">Standard</option><option value="Luxe">Luxe</option><option value="Suite">Suite</option></select></div>
        <div class="mb-3"><label class="form-label fw-bold">Prix par nuit (FCFA)</label><input type="number" name="price_per_night" class="form-control" required></div>
        <button type="submit" class="btn btn-primary w-100">Enregistrer la Chambre</button>
    </form>
</div></div>
@endsection
