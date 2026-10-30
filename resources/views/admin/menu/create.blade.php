@extends('layouts.app')
@section('content')
<div class="pt-3 pb-2 mb-3 border-bottom"><h2><i class="bi bi-egg-fried me-2"></i>Ajouter un Repas / Boisson</h2></div>
@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
<div class="card shadow-sm col-md-6"><div class="card-body">
    <form action="{{ route('admin.menu.store') }}" method="POST">
        @csrf
        <div class="mb-3"><label class="form-label fw-bold">Nom du plat / boisson</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label class="form-label fw-bold">Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <div class="mb-3"><label class="form-label fw-bold">Prix (FCFA)</label><input type="number" name="price" class="form-control" required></div>
        <div class="mb-3"><label class="form-label fw-bold">Point de vente</label><select name="location_type" class="form-select" required><option value="restaurant">Restaurant Principal</option><option value="bar_piscine">Bar Piscine</option></select></div>
        <button type="submit" class="btn btn-success w-100">Ajouter au Menu</button>
    </form>
</div></div>
@endsection
