@extends('layouts.app')

@section('title', 'Gestion de la Facturation - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-calculator me-2"></i>Facturation & Encours Chambres</h1>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">
        <h5 class="card-title mb-0">Clients actuellement présents à l'hôtel</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Numéro Chambre</th>
                        <th>Type de Chambre</th>
                        <th>Prix Fixe Nuitée</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($occupiedRooms as $room)
                        <tr>
                            <td><strong>{{ $room->number }}</strong></td>
                            <td><span class="badge bg-secondary">{{ strtoupper($room->type) }}</span></td>
                            <td>{{ number_format($room->price_per_night, 0, ',', ' ') }} F</td>
                            <td class="text-center">
                                <a href="{{ route('billing.room', $room->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Consulter la Facture / Reçu
                                </a>
                            </td>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucune chambre occupée pour le moment.</td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
