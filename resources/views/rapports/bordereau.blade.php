<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bordereau Officiel de Prise en Charge - {{ $numeroBordereau }}</title>
    {{-- Bootstrap & Lucide Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --bs-font-sans-serif: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --primary-navy: #0f2b48;
            --secondary-teal: #088395;
            --border-color: #cbd5e1;
        }

        body {
            font-family: var(--bs-font-sans-serif);
            background-color: #e2e8f0;
            color: #1e293b;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .font-mono {
            font-family: var(--font-mono);
        }

        /* Top Bar Screen Controls */
        .no-print-bar {
            background-color: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Page Paper Container */
        .paper-container {
            max-width: 1200px;
            margin: 28px auto 40px auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 40px 48px;
            box-sizing: border-box;
        }

        /* Header Clinic */
        .clinic-brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--primary-navy);
            text-transform: uppercase;
        }

        .clinic-subtitle {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            line-height: 1.4;
        }

        .bordereau-badge {
            background: #f8fafc;
            border: 2px solid #0f2b48;
            border-radius: 8px;
            padding: 12px 18px;
        }

        .bordereau-title {
            font-size: 17px;
            font-weight: 800;
            color: var(--primary-navy);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 18px;
        }

        /* Table Styling */
        .table-bordereau {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-top: 15px;
        }

        .table-bordereau th {
            background-color: #0f2b48 !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 8px 10px;
            border: 1px solid #0f2b48;
            vertical-align: middle;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .table-bordereau td {
            padding: 7px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }

        .table-bordereau tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-bordereau tfoot th,
        .table-bordereau tfoot td {
            background-color: #e2e8f0 !important;
            font-weight: 700;
            padding: 8px 10px;
            border: 1px solid #94a3b8;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Words & Certification Box */
        .certification-box {
            border: 1.5px dashed #0f2b48;
            background: #f1f5f9;
            border-radius: 8px;
            padding: 14px 20px;
            margin-top: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Signature Blocks */
        .signature-section {
            margin-top: 35px;
            page-break-inside: avoid;
        }

        .signature-card {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            padding: 12px 14px;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .signature-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }

        /* Print Media Settings */
        @media print {
            body {
                background-color: #ffffff;
                color: #000000;
                font-size: 11px;
            }

            .no-print, .no-print-bar {
                display: none !important;
            }

            .paper-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm 12mm 10mm 12mm;
            }

            .table-bordereau th {
                background-color: #0f2b48 !important;
                color: #ffffff !important;
            }

            .signature-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    {{-- BARRE DE COMMANDE ÉCRAN (Invisible à l'impression) --}}
    <div class="no-print-bar no-print">
        <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('rapports.assurances', request()->query()) }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Retour au Relevé</span>
                </a>
                <div>
                    <span class="badge bg-primary me-2 font-mono">A4 PAYSAGE</span>
                    <strong class="text-white">Bordereau Officiel de Transmission Tiers Payant</strong>
                    <span class="text-white-50 ms-2 small">({{ $numeroBordereau }})</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-success fw-bold d-inline-flex align-items-center gap-2 px-4 shadow-sm">
                    <i data-lucide="printer" style="width: 18px; height: 18px;"></i>
                    <span>Imprimer / Exporter en PDF</span>
                </button>
            </div>
        </div>
    </div>

    {{-- FEUILLE OFFICIELLE DU BORDEREAU A4 --}}
    <div class="paper-container">

        {{-- EN-TÊTE OFFICIEL CLINIQUE & RÉFÉRENCES --}}
        <div class="row align-items-start pb-3 border-bottom mb-3">
            {{-- Identité Clinique --}}
            <div class="col-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div style="background-color: #0f2b48; color: white; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;">
                        +
                    </div>
                    <div>
                        <div class="clinic-brand-title">{{ auth()->user()->clinique->nom ?? 'Clinique Gahambani' }}</div>
                        <div class="small fw-semibold text-muted">Établissement Médico-Chirurgical & Maternité</div>
                    </div>
                </div>
                <div class="clinic-subtitle mt-2">
                    <div><strong>Agrément Ministériel :</strong> N° MS-0452/2020 &bull; <strong>NIF :</strong> 085123456T</div>
                    <div>{{ auth()->user()->clinique->adresse ?? 'Quartier Administratif' }}, {{ auth()->user()->clinique->ville ?? 'Bamako' }} — {{ auth()->user()->clinique->pays ?? 'Mali' }}</div>
                    <div>Tél : {{ auth()->user()->clinique->telephone ?? '(+223) 20 22 00 00' }} &bull; Email : {{ auth()->user()->clinique->email ?? 'contact@clinique.ml' }}</div>
                </div>
            </div>

            {{-- Bloc Titre & N° Bordereau --}}
            <div class="col-5">
                <div class="bordereau-badge text-end">
                    <div class="bordereau-title">Bordereau de Transmission</div>
                    <div class="small text-muted fw-semibold">PRISES EN CHARGE (TIERS PAYANT)</div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">N° Bordereau :</span>
                            <strong class="font-mono text-dark">{{ $numeroBordereau }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Date d'émission :</span>
                            <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Période couverte :</span>
                            <strong class="text-primary">{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CADRE DESTINATAIRE & OBJET --}}
        <div class="row g-3 mb-3">
            <div class="col-7">
                <div class="info-box h-100">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Organisme Assureur Destinataire :</div>
                    <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        @if($assuranceSelected)
                            <span>{{ $assuranceSelected->nom }}</span>
                            <span class="badge bg-primary font-mono">{{ $assuranceSelected->code }}</span>
                        @else
                            <span>RÉCAPITULATIF GLOBAL DES ASSURANCES PARTENAIRES</span>
                        @endif
                    </h6>
                    <div class="small text-muted">
                        Direction du Recouvrement, de la Liquidation & du Contrôle Médical
                    </div>
                </div>
            </div>

            <div class="col-5">
                <div class="info-box h-100">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Objet de la Transmission :</div>
                    <div class="small fw-semibold text-dark mb-1">
                        Transmission des factures et feuilles de soins valant demande de remboursement au titre du Tiers Payant.
                    </div>
                    <div class="small text-muted">
                        Nombre de dossiers joints : <strong class="text-dark">{{ $nbDossiers }} dossier(s)</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DÉTAILLÉ DES DOSSIERS / FEUILLES DE SOINS --}}
        <table class="table-bordereau">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">N°</th>
                    <th style="width: 145px;">Réf. Ticket / Feuille</th>
                    <th style="width: 85px;" class="text-center">Date Visite</th>
                    <th style="width: 180px;">Bénéficiaire (Patient)</th>
                    <th style="width: 140px;">Matricule Assuré</th>
                    <th>Prestations / Actes Dispensés</th>
                    <th style="width: 95px;" class="text-end">Tarif Total</th>
                    <th style="width: 50px;" class="text-center">Taux</th>
                    <th style="width: 95px;" class="text-end">Part Patient</th>
                    <th style="width: 110px;" class="text-end">Part Organisme</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $index => $ticket)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <strong class="font-mono text-dark">{{ $ticket->reference }}</strong>
                            @if(!$assuranceSelected && $ticket->assurance)
                                <br><span class="badge bg-secondary-subtle text-secondary small p-0 font-mono">{{ $ticket->assurance->code ?? $ticket->assurance->nom }}</span>
                            @endif
                        </td>
                        <td class="text-center font-mono">
                            {{ $ticket->date_ticket ? \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y') : $ticket->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                {{ strtoupper($ticket->patient->nom ?? '') }} {{ $ticket->patient->prenom ?? '' }}
                            </div>
                            <span class="small text-muted">Sexe: {{ $ticket->patient->sexe ?? '-' }} | Réf: {{ $ticket->patient->reference ?? '-' }}</span>
                        </td>
                        <td class="font-mono">
                            <strong>{{ $ticket->patient->numero_assure ?? ($ticket->patient->cartesAssurance->first()->reference ?? 'N/A') }}</strong>
                        </td>
                        <td>
                            @if($ticket->ticketDetails && $ticket->ticketDetails->count() > 0)
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($ticket->ticketDetails as $td)
                                        <li class="d-flex justify-content-between align-items-center">
                                            <span>&bull; {{ $td->designation }}</span>
                                            @if($td->quantite > 1)<span class="text-muted ms-1">(x{{ $td->quantite }})</span>@endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted small">Prestations médicales diverses</span>
                            @endif
                        </td>
                        <td class="text-end font-mono">
                            {{ number_format($ticket->montant_total, 0, ',', ' ') }}
                        </td>
                        <td class="text-center font-mono fw-bold text-primary">
                            {{ round(($ticket->montant_assurance / ($ticket->montant_total > 0 ? $ticket->montant_total : 1)) * 100) }}%
                        </td>
                        <td class="text-end font-mono text-muted">
                            {{ number_format($ticket->montant_patient, 0, ',', ' ') }}
                        </td>
                        <td class="text-end font-mono fw-bold text-dark">
                            {{ number_format($ticket->montant_assurance, 0, ',', ' ') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <em>Aucune prestation couverte trouvée pour les critères et la période sélectionnés.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-end text-uppercase">
                        TOTAUX DU BORDEREAU ({{ $nbDossiers }} DOSSIER{{ $nbDossiers > 1 ? 'S' : '' }}) :
                    </th>
                    <th class="text-end font-mono">
                        {{ number_format($totalMontantBrut, 0, ',', ' ') }} FCFA
                    </th>
                    <th></th>
                    <th class="text-end font-mono text-muted">
                        {{ number_format($totalPartPatient, 0, ',', ' ') }} FCFA
                    </th>
                    <th class="text-end font-mono fw-bold fs-6 text-dark">
                        {{ number_format($totalPartAssurance, 0, ',', ' ') }} FCFA
                    </th>
                </tr>
            </tfoot>
        </table>

        {{-- CADRE ARRÊTÉ EN TOUTES LETTRES --}}
        <div class="certification-box">
            <div class="row align-items-center">
                <div class="col-8">
                    <div class="small text-uppercase fw-bold text-muted mb-1">Arrêté du Présent Bordereau :</div>
                    <div class="fw-bold fs-6 text-dark" style="font-style: italic;">
                        « Arrêté le présent bordereau de transmission à la somme nette de : <br>
                        <span class="text-primary text-decoration-underline">{{ $montantEnLettres }}</span> »
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="small text-muted text-uppercase fw-semibold">Net Global à Recouvrer :</div>
                    <div class="fs-4 fw-extrabold font-mono text-dark">
                        {{ number_format($totalPartAssurance, 0, ',', ' ') }} <small class="fs-6">FCFA</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- CADRES DE SIGNATURES & VISAS LÉGAUX --}}
        <div class="signature-section">
            <div class="row g-3">
                {{-- Facturation / Recouvrement --}}
                <div class="col-4">
                    <div class="signature-card">
                        <div>
                            <div class="signature-title">1. Service Facturation & Recouvrement</div>
                            <div class="small text-muted">Dossiers vérifiés et liquidés par :</div>
                            <div class="small fw-semibold mt-1">{{ auth()->user()->name ?? 'Le Responsable Facturation' }}</div>
                        </div>
                        <div class="small text-muted pt-2 border-top">
                            Date : {{ \Carbon\Carbon::now()->format('d/m/Y') }} &bull; Signature :
                        </div>
                    </div>
                </div>

                {{-- Direction Médicale --}}
                <div class="col-4">
                    <div class="signature-card">
                        <div>
                            <div class="signature-title">2. Direction Médicale & Administrative</div>
                            <div class="small text-muted">Vu et certifié sincère et conforme :</div>
                            <div class="small fw-semibold mt-1">Dr. Directrice Médicale / Administrateur</div>
                        </div>
                        <div class="small text-muted pt-2 border-top">
                            Cachet de l'Établissement & Signature :
                        </div>
                    </div>
                </div>

                {{-- Décharge Organisme Assureur --}}
                <div class="col-4">
                    <div class="signature-card">
                        <div>
                            <div class="signature-title">3. Réception Organisme Assureur</div>
                            <div class="small text-muted">Bordereau et pièces justificatives reçus le :</div>
                            <div class="small text-muted mt-1">..... / ..... / 2026 &nbsp;&bull;&nbsp; Par : .......................</div>
                        </div>
                        <div class="small text-muted pt-2 border-top">
                            Visa & Cachet Accusé de Réception :
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PIED DE PAGE LÉGAL --}}
        <div class="text-center text-muted small mt-4 pt-3 border-top" style="font-size: 10px;">
            CLINGEST Santé &bull; Clinique Médico-Chirurgicale Gahambani &bull; Document officiel certifié pour liquidation de Tiers Payant &bull; Émis le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }} &bull; Page 1 / 1
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
