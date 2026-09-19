<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Z - Clôture Caisse #{{ $caisse->id }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'IBM Plex Mono', 'Courier New', Courier, monospace;
            color: #000;
        }

        .receipt-container {
            width: 80mm;
            margin: 20px auto;
            padding: 12px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            line-height: 1.35;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .receipt-header h2 {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .receipt-info {
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .receipt-totals {
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-top: 6px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 16px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 10px;
        }

        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .receipt-container {
                width: 78mm !important;
                margin: 0 !important;
                padding: 2mm !important;
                box-shadow: none !important;
                font-size: 11px !important;
            }

            @page {
                size: 80mm auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Barre d'actions supérieure --}}
    <div class="no-print bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-white"><i data-lucide="printer" class="me-2"></i> Bilan & Ticket de Clôture Journalière (Z de Caisse)</h5>
                <small class="text-light">Session #{{ $caisse->id }} — {{ $caisse->user->name ?? 'Caissier' }}</small>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-success fw-bold">
                    <i data-lucide="printer" class="me-1"></i> Imprimer Ticket Z (80mm)
                </button>
                <a href="{{ route('caisses.show', $caisse) }}" class="btn btn-outline-light">
                    <i data-lucide="arrow-left" class="me-1"></i> Retour aux Détails
                </a>
            </div>
        </div>
    </div>

    {{-- FORMAT TICKET Z 80mm --}}
    <div class="receipt-container">
        <div class="receipt-header">
            <h2>CLINIQUE GAHAMBANI</h2>
            <small>CLINGEST - Gestion Médicale</small><br>
            <small>Mali - Kati</small>
            <div class="mt-2 fw-bold" style="font-size: 13px; border: 1px solid #000; padding: 2px;">
                BILAN DE CLÔTURE DE CAISSE (Z)
            </div>
        </div>

        <div class="receipt-info">
            <div><strong>SESSION N° :</strong> #{{ $caisse->id }}</div>
            <div><strong>CAISSIER :</strong> {{ strtoupper($caisse->user->name ?? 'CAISSE') }}</div>
            <div><strong>OUVERTURE :</strong> {{ $caisse->date_ouverture ? $caisse->date_ouverture->format('d/m/Y H:i') : '--' }}</div>
            <div><strong>CLÔTURE :</strong> {{ $caisse->date_fermeture ? $caisse->date_fermeture->format('d/m/Y H:i') : 'En cours' }}</div>
            <div><strong>STATUT :</strong> {{ strtoupper($caisse->statut) }}</div>
        </div>

        {{-- Bilan Financier --}}
        <div class="receipt-totals">
            <div class="d-flex justify-content-between mb-1">
                <span>FOND DE CAISSE INITIAL :</span>
                <strong>{{ number_format($caisse->fonds_initial, 0, ',', ' ') }} FCFA</strong>
            </div>

            <div class="d-flex justify-content-between mb-1 text-success">
                <span>+ RECETTES / ENCAISSEMENTS :</span>
                <strong>+{{ number_format($caisse->total_entrees, 0, ',', ' ') }} FCFA</strong>
            </div>

            <div class="d-flex justify-content-between mb-1 text-danger">
                <span>- DÉPENSES / SORTIES ESPÈCES :</span>
                <strong>-{{ number_format($caisse->total_sorties, 0, ',', ' ') }} FCFA</strong>
            </div>

            <hr style="border-top: 1px dashed #000; margin: 6px 0;">

            <div class="d-flex justify-content-between mb-1 fw-bold" style="font-size: 13px;">
                <span>SOLDE THÉORIQUE ATTENDU :</span>
                <span>{{ number_format($caisse->solde_theorique, 0, ',', ' ') }} FCFA</span>
            </div>

            <div class="d-flex justify-content-between mb-1 fw-bold">
                <span>ESPÈCES PHYSIQUES COMPTÉES :</span>
                <span>{{ number_format($caisse->solde_physique ?? $caisse->solde_theorique, 0, ',', ' ') }} FCFA</span>
            </div>

            <hr style="border-top: 1px solid #000; margin: 6px 0;">

            @php $ecart = (float)($caisse->ecart ?? 0); @endphp
            <div class="d-flex justify-content-between fw-bold {{ $ecart < 0 ? 'text-danger' : ($ecart > 0 ? 'text-primary' : 'text-success') }}" style="font-size: 13px;">
                <span>ÉCART DE CAISSE :</span>
                <span>
                    @if($ecart == 0)
                        0 FCFA (CONFORME ✓)
                    @elseif($ecart < 0)
                        {{ number_format($ecart, 0, ',', ' ') }} FCFA (MANQUANT)
                    @else
                        +{{ number_format($ecart, 0, ',', ' ') }} FCFA (EXCÉDENT)
                    @endif
                </span>
            </div>
        </div>

        @if($caisse->observation)
            <div class="mt-2 p-1 border text-start" style="font-size: 10px;">
                <strong>Observation :</strong> {{ $caisse->observation }}
            </div>
        @endif

        {{-- Signatures --}}
        <div class="mt-4 pt-2 border-top d-flex justify-content-between" style="font-size: 10px;">
            <div class="text-center" style="width: 45%;">
                <div>Signature Caissier :</div>
                <div style="height: 35px;"></div>
                <div style="border-top: 1px dotted #000;">{{ $caisse->user->name ?? 'Caissier' }}</div>
            </div>
            <div class="text-center" style="width: 45%;">
                <div>Visa Responsable / Direction :</div>
                <div style="height: 35px;"></div>
                <div style="border-top: 1px dotted #000;">Pour Approbation</div>
            </div>
        </div>

        <div class="receipt-footer">
            <small>Imprimé le {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</small><br>
            <small>CLINGEST - Contrôle Financier Interne</small>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
</body>
</html>
