<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Police - {{ $reservation->customer->full_name }}</title>
    <link href="https://jsdelivr.net" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
        }
        .sheet {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header-box {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f1f5f9;
            padding: 6px 12px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            border-left: 4px solid #0f172a;
            margin-bottom: 15px;
        }
        .field-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
        }
        .field-value {
            font-size: 1rem;
            font-weight: 700;
            border-bottom: 1px dotted #94a3b8;
            padding-bottom: 4px;
            min-height: 28px;
        }
        .signature-box {
            height: 90px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            font-size: 0.8rem;
            color: #64748b;
        }
        @media print {
            body { background: #ffffff; }
            .sheet { margin: 0; border: none; box-shadow: none; padding: 15px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- Barre d'actions (masquée à l'impression) -->
    <div class="container text-center mt-3 no-print">
        <button onclick="window.print()" class="btn btn-primary fw-bold px-4 rounded-pill shadow-sm me-2">
            Imprimer la Fiche de Police
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 rounded-pill">
            Fermer
        </button>
    </div>

    <!-- Document officiel -->
    <div class="sheet">
        <div class="header-box d-flex justify-content-between align-items-start">
            <div>
                <h4 class="fw-bold mb-1">{{ config('app.name', 'HÔTEL MANAGER') }}</h4>
                <div class="small text-muted">Registre Réglementaire des Voyageurs</div>
                <div class="small text-muted">Abidjan, Côte d'Ivoire</div>
            </div>
            <div class="text-end">
                <div class="badge bg-dark fs-6 px-3 py-2 mb-1">FICHE DE POLICE N° {{ str_pad($reservation->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="small text-muted">Date d'édition : {{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- 1. Identité du voyageur -->
        <div class="section-title">1. Identité du Voyageur</div>
        <div class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="field-label">Nom et Prénoms</div>
                <div class="field-value">{{ strtoupper($reservation->customer->full_name) }}</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Nationalité</div>
                <div class="field-value">{{ $reservation->customer->nationality ?? 'Ivoirienne' }}</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Nature de la Pièce</div>
                <div class="field-value">{{ $reservation->customer->id_type ?? 'CNI' }}</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Numéro de la Pièce</div>
                <div class="field-value">{{ $reservation->customer->id_card_number ?? 'Non renseigné' }}</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Téléphone</div>
                <div class="field-value">{{ $reservation->customer->phone ?? 'Non renseigné' }}</div>
            </div>
            <div class="col-12">
                <div class="field-label">Domicile Habituel / Adresse</div>
                <div class="field-value">{{ $reservation->customer->address ?? '' }}</div>
            </div>
        </div>

        <!-- 2. Détails du séjour -->
        <div class="section-title">2. Renseignements sur le Séjour</div>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="field-label">Chambre Attribuée</div>
                <div class="field-value">Chambre {{ $reservation->room->number }} ({{ $reservation->room->type }})</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Date d'Arrivée</div>
                <div class="field-value">{{ $reservation->check_in_date->format('d/m/Y') }}</div>
            </div>
            <div class="col-md-4">
                <div class="field-label">Date de Départ Prévue</div>
                <div class="field-value">{{ $reservation->check_out_date->format('d/m/Y') }} ({{ $reservation->nights }} nuit(s))</div>
            </div>
            <div class="col-md-6">
                <div class="field-label">Venant de (Provenance)</div>
                <div class="field-value">{{ $reservation->provenance ?? '' }}</div>
            </div>
            <div class="col-md-6">
                <div class="field-label">Allant à (Destination)</div>
                <div class="field-value">{{ $reservation->destination ?? '' }}</div>
            </div>
        </div>

        <!-- 3. Signatures -->
        <div class="section-title">3. Validation et Signatures</div>
        <div class="row g-3 mt-2">
            <div class="col-6">
                <div class="signature-box">
                    Signature du Client :
                </div>
            </div>
            <div class="col-6">
                <div class="signature-box">
                    Cachet de l'Établissement & Réception :
                </div>
            </div>
        </div>
    </div>

</body>
</html>
