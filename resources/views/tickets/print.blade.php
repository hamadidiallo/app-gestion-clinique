<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Consultation - {{ $ticket->reference }}</title>

    {{-- Bootstrap 5 CSS pour l'aperçu écran --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    {{-- Style CSS optimisé pour Imprimante Ticket Thermique 80mm et impression --}}
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'IBM Plex Mono', 'Courier New', Courier, monospace;
            color: #000;
        }

        /* Conteneur simulant le rouleau de ticket 80mm à l'écran */
        .ticket-receipt {
            width: 80mm;
            margin: 20px auto;
            padding: 12px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-size: 12px;
            line-height: 1.3;
        }

        .ticket-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .ticket-header h2 {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .ticket-info {
            border-bottom: 1px dashed #000;
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .ticket-table th, .ticket-table td {
            text-align: left;
            padding: 3px 0;
            font-size: 11px;
        }

        .ticket-table th.right, .ticket-table td.right {
            text-align: right;
        }

        .ticket-totals {
            border-top: 1px dashed #000;
            padding-top: 6px;
            margin-top: 6px;
        }

        .ticket-footer {
            text-align: center;
            margin-top: 12px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 10px;
        }

        /* Directives d'impression CSS (@media print) */
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }

            .ticket-receipt {
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

    {{-- Barre d'actions supérieure visible uniquement à l'écran --}}
    <div class="no-print bg-dark text-white p-3 shadow-sm mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-white"><i data-lucide="printer" class="me-2"></i> Impression Ticket Thermique & Facture</h5>
                <small class="text-light">Référence: {{ $ticket->reference }}</small>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-success fw-bold">
                    <i data-lucide="printer" class="me-1"></i> Imprimer Ticket (80mm)
                </button>
                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-light">
                    <i data-lucide="arrow-left" class="me-1"></i> Retour aux Détails
                </a>
            </div>
        </div>
    </div>

    {{-- FORMAT TICKET THERMIQUE (80mm) --}}
    <div class="ticket-receipt">
        {{-- En-tête de la Clinique --}}
        <div class="ticket-header">
            @if(!empty($ticket->clinique->logo))
                <div class="mb-1 text-center">
                    <img src="{{ asset('storage/' . $ticket->clinique->logo) }}" alt="Logo" style="max-height: 40px; max-width: 140px; object-fit: contain; filter: grayscale(100%);">
                </div>
            @endif
            <h2>{{ strtoupper($ticket->clinique->nom ?? config('app.name')) }}</h2>
            <small>{{ $ticket->clinique->ville ?? 'Mali' }} · {{ $ticket->clinique->telephone ?? '' }}</small>
        </div>

        @php
            $printService = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
            $printMedecin = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
        @endphp
        {{-- Métadonnées du Ticket --}}
        <div class="ticket-info">
            <div><strong>RECU N°:</strong> {{ $ticket->reference }}</div>
            <div><strong>DATE:</strong> {{ \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y H:i') }}</div>
            <div><strong>EXPIRATION:</strong> {{ $ticket->date_expiration ? \Carbon\Carbon::parse($ticket->date_expiration)->format('d/m/Y') : '7 Jours' }}</div>
            <hr style="border-top: 1px dotted #000; margin: 4px 0;">
            <div><strong>PATIENT:</strong> {{ strtoupper($ticket->patient->nom ?? 'INCONNU') }} {{ $ticket->patient->prenom ?? '' }}</div>
            <div><strong>SEXE:</strong> {{ $ticket->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}</div>
            @if($ticket->assurance)
                <div><strong>ASSURANCE:</strong> {{ strtoupper($ticket->assurance->nom) }}</div>
            @endif
            @if($printService)
                <div><strong>SERVICE:</strong> {{ strtoupper($printService->nom) }}</div>
            @endif
            @if($printMedecin)
                <div><strong>MEDECIN:</strong> DR {{ strtoupper($printMedecin->nom) }} {{ strtoupper($printMedecin->prenom) }}</div>
                <div><strong>SPECIALITE:</strong> {{ strtoupper($printMedecin->specialite ?? 'GENERALISTE') }}</div>
            @endif
        </div>

        {{-- Tableau des actes, médicaments et hospitalisations --}}
        <table class="ticket-table">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th>DESIGNATION</th>
                    <th class="right">PU</th>
                    <th class="right">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ticket->details as $detail)
                    <tr>
                        <td>
                            <strong>{{ $detail->libelle }}</strong><br>
                            <small>[{{ strtoupper($detail->type_item ?? 'acte') }}] &times; {{ number_format($detail->quantite, 0) }}</small>
                        </td>
                        <td class="right" style="vertical-align: top;">{{ number_format($detail->prix_unitaire, 0, ',', ' ') }}</td>
                        <td class="right" style="vertical-align: top;">{{ number_format($detail->montant_total, 0, ',', ' ') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Consultation / Acte Médical Standard</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Résumé Financier --}}
        <div class="ticket-totals">
            <div class="d-flex justify-content-between">
                <span>TOTAL BRUT:</span>
                <strong>{{ number_format($ticket->montant_total, 0, ',', ' ') }} FCFA</strong>
            </div>

            @if($ticket->montant_assurance > 0)
                <div class="d-flex justify-content-between">
                    <span>PART ASSURANCE ({{ $ticket->assurance ? $ticket->assurance->nom : 'TIERS' }}):</span>
                    <span>-{{ number_format($ticket->montant_assurance, 0, ',', ' ') }} FCFA</span>
                </div>
            @endif

            <div class="d-flex justify-content-between mt-1" style="font-size: 13px; font-weight: bold; border-top: 1px dashed #000; padding-top: 4px;">
                <span>NET PATIENT:</span>
                <span>{{ number_format($ticket->montant_patient, 0, ',', ' ') }} FCFA</span>
            </div>

            <div class="d-flex justify-content-between mt-1">
                <span>MONTANT ENCAISSÉ:</span>
                <span>{{ number_format($ticket->montant_paye, 0, ',', ' ') }} FCFA</span>
            </div>

            @if($ticket->reste_a_payer > 0)
                <div class="d-flex justify-content-between text-danger">
                    <span>RESTE DÛ (DETTE):</span>
                    <strong>{{ number_format($ticket->reste_a_payer, 0, ',', ' ') }} FCFA</strong>
                </div>
            @else
                <div class="d-flex justify-content-between text-success">
                    <span>SOLDE:</span>
                    <strong>SOLDÉ / PAYÉ</strong>
                </div>
            @endif
        </div>

        {{-- Pied de ticket --}}
        <div class="ticket-footer">
            <p class="mb-1"><strong>STATUT: {{ strtoupper(str_replace('_', ' ', $ticket->statut)) }}</strong></p>
            <p class="mb-1">Ticket valable 7 jours à compter de sa date d'émission.</p>
            <small>Caissier: {{ $ticket->user->name ?? 'Guichetier' }}</small><br>
            <small>*** Prompt et complet rétablissement ***</small>
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
