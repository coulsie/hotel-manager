@extends('layouts.app')

@section('title', 'QR Codes Chambres & Demandes Clients - Hotel Manager')

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 pb-3 mb-4 border-bottom d-print-none">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-qr-code-scan text-primary me-2"></i>Room Directory QR Codes & Demandes Clients
            </h3>
            <p class="text-muted small mb-0">Imprimez les QR Codes des chambres et traitez les demandes envoyées depuis les smartphones des clients.</p>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-printer-fill text-warning me-1"></i> Imprimer tous les QR Codes
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-print-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- SECTION 1 : DEMANDES CLIENTS EN ATTENTE -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden d-print-none">
        <div class="card-header bg-warning-subtle text-dark py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-uppercase">
                <i class="bi bi-bell-fill text-danger me-2"></i>Demandes Clients en Chambre à Traiter
            </h6>
            <span class="badge bg-danger rounded-pill px-3">{{ $pendingRequests->count() }} en attente</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-uppercase text-muted">
                        <tr>
                            <th class="ps-4">Heure</th>
                            <th>Chambre</th>
                            <th>Type de Demande</th>
                            <th>Détails Client</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRequests as $req)
                            <tr>
                                <td class="ps-4 text-muted">{{ \Carbon\Carbon::parse($req->created_at)->format('d/m H:i') }}</td>
                                <td><span class="badge bg-primary fs-6">{{ $req->room_number }}</span></td>
                                <td class="fw-bold text-uppercase text-dark">{{ str_replace('_', ' ', $req->type) }}</td>
                                <td>{{ $req->details ?: 'Aucune précision supplémentaire' }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('room.directory.complete', $req->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                            <i class="bi bi-check2-circle me-1"></i> Marquer traitée
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    Aucune demande client en attente pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2 : GRILLE DES QR CODES VECTORIELS (100% HORS-LIGNE) -->
    <div class="row g-4">
        @foreach($rooms as $room)
            @php
                $portalUrl = route('guest.portal', $room->id);
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card border shadow-sm rounded-4 text-center p-3 h-100 bg-white">
                    <div class="fw-bold text-uppercase text-primary small mb-1">Hotel Manager Resort</div>
                    <h5 class="fw-bold text-dark mb-2">{{ $room->number }}</h5>

                    <!-- QR Code SVG généré localement par Laravel -->
                    <div class="my-2 d-flex justify-content-center">
                        <div class="border p-2 rounded-3 bg-white">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(150)->margin(1)->generate($portalUrl) !!}
                        </div>
                    </div>

                    <p class="small fw-semibold text-dark mb-1">Scannez pour accéder au Room Service</p>
                    <p class="text-muted mb-2" style="font-size: 0.72rem;">Carte Restaurant • Ménage • Conciergerie</p>

                    <div class="mt-auto d-print-none">
                        <a href="{{ $portalUrl }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                            <i class="bi bi-phone me-1"></i> Tester sur mobile
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
