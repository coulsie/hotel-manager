@extends('layouts.app')

@section('title', 'Historique IoT - Hotel Manager')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-broadcast me-2 text-danger"></i>Journal de Traçabilité des Accès IoT</h1>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Tous les scans de cartes magnétiques</h5>
        <span class="badge bg-danger animate-pulse"><i class="bi bi-circle-fill me-1"></i> Surveillance à distance active</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Date & Heure du scan</th>
                        <th>N° Chambre</th>
                        <th>Code Unique RFID (Carte)</th>
                        <th>Action détectée</th>
                        <th>Statut de la serrure</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->scanned_at)->format('d/m/Y à H:i:s') }}</td>
                            <td><strong class="text-dark">{{ $log->room->number ?? 'N/A' }}</strong></td>
                            <td><code class="text-secondary">{{ $log->magneticCard->card_uid ?? 'UID INCONNU' }}</code></td>
                            <td>
                                @if($log->action === 'access_granted')
                                    <i class="bi bi-unlock-fill text-success me-2"></i>Déverrouillage électronique
                                @else
                                    <i class="bi bi-lock-fill text-danger me-2"></i>Refus d'ouverture (Badge invalide)
                                @endif
                            </td>
                            <td>
                                @if($log->action === 'access_granted')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Accès Autorisé</span>
                                @else
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Accès Bloqué</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Aucun enregistrement IoT reçu pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Liens de pagination de Laravel -->
<div class="d-flex justify-content-center">
    {{ $logs->links() }}
</div>
@endsection
