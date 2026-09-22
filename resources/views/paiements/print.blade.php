<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Caisse - {{ $paiement->reference }}</title>

    {{-- Bootstrap 5 CSS pour l'aperçu écran --}}
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
            margin-top: 12px;
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

    {{-- Barre d'actions supérieure écran --}}
    <div class="no-print bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-white"><i data-lucide="printer" class="me-2"></i> Reçu de Caisse & Quittance de Règlement</h5>
                <small class="text-light">Référence Reçu : {{ $paiement->reference }}</small>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-success fw-bold">
                    <i data-lucide="printer" class="me-1"></i> Imprimer Reçu (80mm)
                </button>
                <a href="{{ route('paiements.show', $paiement) }}" class="btn btn-outline-light">
                    <i data-lucide="arrow-left" class="me-1"></i> Retour aux Détails
                </a>
            </div>
        </div>
    </div>

    {{-- FORMAT DU TICKET THERMIQUE 80mm --}}
    <div class="receipt-container">
        <div class="receipt-header">
            @if(!empty($paiement->clinique->logo))
                <div class="mb-1 text-center">
                    <img src="{{ asset('storage/' . $paiement->clinique->logo) }}" alt="Logo" style="max-height: 40px; max-width: 140px; object-fit: contain; filter: grayscale(100%);">
                </div>
            @endif
            <h2>{{ strtoupper($paiement->clinique->nom ?? config('app.name')) }}</h2>
            <small>{{ $paiement->clinique->ville ?? 'Mali' }} · {{ $paiement->clinique->telephone ?? '' }}</small>
            <div class="mt-1 fw-bold" style="font-size: 13px;">QUITTANCE DE PAIEMENT</div>
        </div>

        @php
            $ticket = $paiement->ticket;
            $patient = $ticket?->patient;
        @endphp

        <div class="receipt-info">
            <div><strong>REÇU N° :</strong> {{ $paiement->reference }}</div>
            <div><strong>DATE :</strong> {{ $paiement->date_paiement ? $paiement->date_paiement->format('d/m/Y H:i') : $paiement->created_at->format('d/m/Y H:i') }}</div>
            <div><strong>CAISSIER :</strong> {{ strtoupper($paiement->user->name ?? 'CAISSE') }}</div>
            <hr style="border-top: 1px dotted #000; margin: 4px 0;">
            <div><strong>PATIENT :</strong> {{ strtoupper($patient->nom ?? 'INCONNU') }} {{ $patient->prenom ?? '' }}</div>
            @if($patient && $patient->telephone)
                <div><strong>TÉL :</strong> {{ $patient->telephone }}</div>
            @endif
            @if($ticket)
                <div><strong>TICKET ASSOCIÉ :</strong> #{{ $ticket->reference }}</div>
            @endif
            <div><strong>MODE :</strong> {{ strtoupper($paiement->modePaiement->nom ?? 'ESPÈCES') }}</div>
        </div>

        {{-- Synthèse Financière --}}
        <div class="receipt-totals">
            @if($ticket)
                <div class="d-flex justify-content-between mb-1">
                    <span>TOTAL DU TICKET :</span>
                    <strong>{{ number_format($ticket->montant_patient, 0, ',', ' ') }} FCFA</strong>
                </div>
            @endif

            <div class="d-flex justify-content-between mb-1" style="border-top: 1px dashed #000; padding-top: 4px;">
                <span>SOMME VERSÉE :</span>
                <strong>{{ number_format($paiement->montant_recu, 0, ',', ' ') }} FCFA</strong>
            </div>

            <div class="d-flex justify-content-between mb-1 font-weight-bold" style="font-size: 13px;">
                <span>MONTANT IMPUTÉ :</span>
                <strong>{{ number_format($paiement->montant_impute, 0, ',', ' ') }} FCFA</strong>
            </div>

            @if($paiement->montant_rendu > 0)
                <div class="d-flex justify-content-between mb-1">
                    <span>MONNAIE RENDUE :</span>
                    <span>{{ number_format($paiement->montant_rendu, 0, ',', ' ') }} FCFA</span>
                </div>
            @endif

            <hr style="border-top: 1px dashed #000; margin: 6px 0;">

            @if($ticket)
                @if($ticket->reste_a_payer <= 0)
                    <div class="d-flex justify-content-between fw-bold text-success" style="font-size: 13px;">
                        <span>NOUVEAU SOLDE :</span>
                        <span>0 FCFA (SOLDÉ ✓)</span>
                    </div>
                @else
                    <div class="d-flex justify-content-between fw-bold text-danger">
                        <span>RESTE DÛ :</span>
                        <span>{{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FCFA</span>
                    </div>
                @endif
            @endif
        </div>

        {{-- Pied de quittance --}}
        <div class="receipt-footer">
            <p class="mb-1"><strong>REÇU LIBÉRATOIRE DE CAISSE</strong></p>
            <p class="mb-1">Conservez ce reçu comme preuve de paiement.</p>
            <small>*** Merci de votre confiance ***</small>
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
