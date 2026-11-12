<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bordereau de Liquidation des Honoraires Médecins - {{ $numeroBordereau }}</title>
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
            --doctor-green: #0f766e;
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
            border: 2px solid #0f766e;
            border-radius: 8px;
            padding: 12px 18px;
        }

        .bordereau-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.04em;
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
            background-color: #0f766e !important;
            color: #ffffff !important;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 8px 10px;
            border: 1px solid #0f766e;
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
            border: 1.5px dashed #0f766e;
            background: #f0fdfa;
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
                background-color: #0f766e !important;
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
                <a href="{{ route('rapports.medecins', request()->query()) }}" class="btn btn-sm btn-outline-light d-inline-flex align-items-center gap-1">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                    <span>Retour au Relevé</span>
                </a>
                <div>
                    <span class="badge bg-teal text-white me-2 font-mono" style="background-color: #0f766e;">A4 PAYSAGE</span>
                    <strong class="text-white">Bordereau de Liquidation des Honoraires & Rétrocessions</strong>
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
                    <div style="background-color: #0f766e; color: white; width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;">
                        +
                    </div>
                    <div>
                        <div class="clinic-brand-title">{{ auth()->user()->clinique->nom ?? config('app.name') }}</div>
                        <div class="small fw-semibold text-muted">Établissement Médico-Chirurgical & Maternité</div>
                    </div>
                </div>
                <div class="clinic-subtitle mt-2">
                    <div><strong>Agrément Ministériel :</strong> N° MS-0452/2020 &bull; <strong>NIF :</strong> 085123456T</div>
                    <div>{{ auth()->user()->clinique->adresse ?? '' }}, {{ auth()->user()->clinique->ville ?? '' }} — {{ auth()->user()->clinique->pays ?? '' }}</div>
                    <div>Tél : {{ auth()->user()->clinique->telephone ?? '(+223) 20 22 00 00' }} &bull; Email : {{ auth()->user()->clinique->email ?? 'contact@clinique.ml' }}</div>
                </div>
            </div>

            {{-- Bloc Titre & N° Bordereau --}}
            <div class="col-5">
                <div class="bordereau-badge text-end">
                    <div class="bordereau-title">Décompte d'Honoraires</div>
                    <div class="small text-muted fw-semibold">RÉTROCESSION MÉDICALE PRATICIEN</div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">N° Bordereau :</span>
                            <strong class="font-mono text-dark">{{ $numeroBordereau }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Date d'édition :</span>
                            <strong>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Période liquidée :</span>
                            <strong style="color: #0f766e;">{{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CADRE PRATICIEN & OBJET --}}
        <div class="row g-3 mb-3">
            <div class="col-7">
                <div class="info-box h-100">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Médecin Praticien Bénéficiaire :</div>
                    <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        @if($medecinSelected)
                            <span>Dr. {{ $medecinSelected->nom }} {{ $medecinSelected->prenom }}</span>
                            <span class="badge font-mono" style="background-color: #0f766e; color: white;">{{ $medecinSelected->code }}</span>
                        @else
                            <span>RÉCAPITULATIF GLOBAL — TOUS LES MÉDECINS PRESTATAIRES</span>
                        @endif
                    </h6>
                    <div class="small text-muted">
                        @if($medecinSelected)
                            Spécialité : <strong>{{ $medecinSelected->specialite ?? 'Médecine Générale' }}</strong> &bull; Tél : {{ $medecinSelected->telephone ?? 'Non renseigné' }}
                        @else
                            Corps médical et spécialistes conventionnés de la clinique
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-5">
                <div class="info-box h-100">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Objet de la Liquidation :</div>
                    <div class="small fw-semibold text-dark mb-1">
                        Liquidation de la quote-part des honoraires sur actes et consultations réalisés.
                    </div>
                    <div class="small text-muted">
                        Volume d'actes liquidés : <strong class="text-dark">{{ $nbActes }} acte(s) médical(aux)</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLEAU DÉTAILLÉ DES ACTES ET HONORAIRES --}}
        <table class="table-bordereau">
            <thead>
                <tr>
                    <th style="width: 32px;" class="text-center">N°</th>
                    <th style="width: 140px;">Réf. Prestation</th>
                    <th style="width: 105px;" class="text-center">Date & Heure</th>
                    @if(!$medecinSelected)
                        <th style="width: 170px;">Médecin Prestataire</th>
                    @endif
                    <th style="width: 180px;">Patient Bénéficiaire</th>
                    <th>Acte / Soin Médical Réalisé</th>
                    <th style="width: 100px;" class="text-end">Montant Acte</th>
                    <th style="width: 60px;" class="text-center">Taux %</th>
                    <th style="width: 110px;" class="text-end">Part Clinique</th>
                    <th style="width: 120px;" class="text-end">Honoraires Dus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($prestations as $index => $prestation)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <strong class="font-mono text-dark">{{ $prestation->reference }}</strong>
                        </td>
                        <td class="text-center font-mono">
                            {{ $prestation->date_prestation ? $prestation->date_prestation->format('d/m/Y H:i') : ($prestation->created_at ? $prestation->created_at->format('d/m/Y H:i') : '-') }}
                        </td>
                        @if(!$medecinSelected)
                            <td>
                                <strong>Dr. {{ $prestation->medecin->nom ?? '' }} {{ $prestation->medecin->prenom ?? '' }}</strong>
                                <br><small class="text-muted font-mono">{{ $prestation->medecin->code ?? '' }}</small>
                            </td>
                        @endif
                        <td>
                            <div class="fw-bold text-dark">
                                {{ strtoupper($prestation->patient->nom ?? '') }} {{ $prestation->patient->prenom ?? '' }}
                            </div>
                            <span class="small text-muted">Réf: {{ $prestation->patient->reference ?? '-' }}</span>
                        </td>
                        <td>
                            <strong>{{ $prestation->acte->nom ?? $prestation->type ?? 'Acte médical' }}</strong>
                            @if($prestation->service)
                                <br><small class="text-muted">{{ $prestation->service->nom }}</small>
                            @endif
                        </td>
                        <td class="text-end font-mono">
                            {{ number_format($prestation->montant, 0, ',', ' ') }}
                        </td>
                        <td class="text-center font-mono fw-bold" style="color: #0f766e;">
                            {{ number_format($prestation->pourcentage_medecin ?? 50, 0) }}%
                        </td>
                        <td class="text-end font-mono text-muted">
                            {{ number_format($prestation->part_clinique, 0, ',', ' ') }}
                        </td>
                        <td class="text-end font-mono fw-bold text-dark">
                            {{ number_format($prestation->part_medecin, 0, ',', ' ') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $medecinSelected ? 9 : 10 }}" class="text-center py-4 text-muted">
                            <em>Aucune prestation médicale répertoriée pour les critères et la période sélectionnés.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="{{ $medecinSelected ? 5 : 6 }}" class="text-end text-uppercase">
                        TOTAUX DES HONORAIRES ({{ $nbActes }} ACTE{{ $nbActes > 1 ? 'S' : '' }}) :
                    </th>
                    <th class="text-end font-mono">
                        {{ number_format($totalMontantBrut, 0, ',', ' ') }} FCFA
                    </th>
                    <th></th>
                    <th class="text-end font-mono text-muted">
                        {{ number_format($totalPartClinique, 0, ',', ' ') }} FCFA
                    </th>
                    <th class="text-end font-mono fw-bold fs-6 text-dark">
                        {{ number_format($totalPartMedecin, 0, ',', ' ') }} FCFA
                    </th>
                </tr>
            </tfoot>
        </table>

        {{-- CADRE ARRÊTÉ EN TOUTES LETTRES --}}
        <div class="certification-box">
            <div class="row align-items-center">
                <div class="col-8">
                    <div class="small text-uppercase fw-bold text-muted mb-1">Arrêté du Décompte d'Honoraires :</div>
                    <div class="fw-bold fs-6 text-dark" style="font-style: italic;">
                        « Arrêté le présent décompte de rétrocession à la somme nette à verser de : <br>
                        <span style="color: #0f766e;" class="text-decoration-underline">{{ $montantEnLettres }}</span> »
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="small text-muted text-uppercase fw-semibold">Net d'Honoraires Liquidé :</div>
                    <div class="fs-4 fw-extrabold font-mono text-dark">
                        {{ number_format($totalPartMedecin, 0, ',', ' ') }} <small class="fs-6">FCFA</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- CADRES DE SIGNATURES & VISAS LÉGAUX --}}
        <div class="signature-section">
            <div class="row g-3">
                {{-- Service Comptabilité --}}
                <div class="col-4">
                    <div class="signature-card">
                        <div>
                            <div class="signature-title">1. Service Comptabilité & Caisse</div>
                            <div class="small text-muted">Calculé et vérifié par :</div>
                            <div class="small fw-semibold mt-1">{{ auth()->user()->name ?? 'Le Responsable Financier' }}</div>
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
                            <div class="signature-title">2. Direction Médicale / Validation</div>
                            <div class="small text-muted">Bon pour mandatement & règlement :</div>
                            <div class="small fw-semibold mt-1">Dr. Directrice Médicale / Administrateur</div>
                        </div>
                        <div class="small text-muted pt-2 border-top">
                            Cachet de l'Établissement & Signature :
                        </div>
                    </div>
                </div>

                {{-- Médecin Bénéficiaire --}}
                <div class="col-4">
                    <div class="signature-card">
                        <div>
                            <div class="signature-title">3. Le Médecin Bénéficiaire</div>
                            <div class="small text-muted">Mention manuscrite « Reçu pour acquit » :</div>
                            <div class="small fw-semibold mt-1">
                                @if($medecinSelected)
                                    Dr. {{ $medecinSelected->nom }} {{ $medecinSelected->prenom }}
                                @else
                                    Le(s) Praticien(s) Concerné(s)
                                @endif
                            </div>
                        </div>
                        <div class="small text-muted pt-2 border-top">
                            Date : ..... / ..... / 2026 &bull; Signature :
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PIED DE PAGE LÉGAL --}}
        <div class="text-center text-muted small mt-4 pt-3 border-top" style="font-size: 10px;">
            {{ config('app.name') }} &bull; {{ auth()->user()->clinique->nom ?? '' }} &bull; Document officiel de liquidation des honoraires médicaux &bull; Émis le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }} &bull; Page 1 / 1
        </div>

    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
