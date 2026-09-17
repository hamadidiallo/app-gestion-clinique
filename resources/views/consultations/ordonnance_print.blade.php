<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnance Médicale - {{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'IBM Plex Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .prescription-container {
            max-width: 800px;
            margin: 20px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            min-height: 950px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .clinic-header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .patient-box {
            background-color: #e2f1ef;
            border-left: 4px solid #0f766e;
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 25px;
        }
        .rx-symbol {
            font-family: Georgia, serif;
            font-size: 2.2rem;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 15px;
        }
        .drug-item {
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .drug-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }
        .drug-dosage {
            font-size: 0.95rem;
            color: #475569;
            margin-left: 8px;
        }
        .drug-posology {
            font-size: 0.95rem;
            color: #1e293b;
            padding-left: 15px;
            margin-top: 4px;
        }
        .signature-section {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        @media print {
            body {
                background: white;
            }
            .prescription-container {
                box-shadow: none;
                padding: 20px;
                margin: 0;
                max-width: 100%;
                min-height: auto;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

{{-- Boutons d'impression en haut --}}
<div class="container text-center my-3 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow fw-bold px-4 me-2">
        <i data-lucide="printer" class="me-2"></i> Imprimer l'Ordonnance
    </button>
    <a href="{{ route('consultations.show', $consultation) }}" class="btn btn-outline-secondary btn-lg">
        <i data-lucide="arrow-left" class="me-1"></i> Retour à la Consultation
    </a>
</div>

<div class="prescription-container">
    <div>
        {{-- En-tête de la Clinique --}}
        <div class="clinic-header d-flex justify-content-between align-items-start">
            <div>
                <h2 class="h4 text-primary fw-bold mb-1">
                    <i data-lucide="building-2" class="me-2"></i>CLINIQUE MÉDICO-CHIRURGICALE
                </h2>
                <p class="small text-muted mb-0">Consultations Générales & Spécialisées - Urgences 24h/24</p>
                <p class="small text-muted mb-0">Bamako, Mali • Tél: (+223) 20 22 00 00 / (+223) 70 00 00 00</p>
            </div>
            <div class="text-end">
                <span class="badge bg-light text-dark border font-monospace fs-6">N° {{ $consultation->ordonnance->reference }}</span>
                <p class="small text-muted mt-1 mb-0">Date : <strong>{{ $consultation->ordonnance->date_ordonnance->format('d/m/Y') }}</strong></p>
            </div>
        </div>

        {{-- Encadré Médecin & Patient --}}
        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="p-2 border rounded bg-white h-100">
                    <span class="small text-muted d-block text-uppercase fw-bold">Médecin Prescripteur :</span>
                    @if($consultation->medecin)
                        <strong class="text-dark fs-6">Dr {{ $consultation->medecin->nom }} {{ $consultation->medecin->prenom }}</strong>
                        <div class="small text-primary fw-semibold">{{ $consultation->medecin->specialite ?? 'Médecine Générale' }}</div>
                    @else
                        <strong class="text-dark fs-6">Le Médecin de Garde</strong>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <div class="patient-box h-100 mb-0">
                    <span class="small text-muted d-block text-uppercase fw-bold">Patient(e) :</span>
                    <strong class="text-dark fs-6">{{ $consultation->patient->nom }} {{ $consultation->patient->prenom }}</strong>
                    <div class="small text-muted">
                        Sexe : {{ $consultation->patient->sexe == 'M' ? 'Masculin' : 'Féminin' }}
                        @if($consultation->patient->telephone)
                            • Tél : {{ $consultation->patient->telephone }}
                        @endif
                    </div>
                    @if($consultation->patient->assurance)
                        <div class="small text-success fw-bold mt-1">
                            <i data-lucide="shield-check" class="me-1"></i> Organisme : {{ $consultation->patient->assurance->nom }} (Mat : {{ $consultation->patient->numero_assure ?? '-' }})
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Symbole Rp / Rx --}}
        <div class="rx-symbol">℞</div>

        {{-- Lignes de Prescription Médicamenteuse --}}
        <div class="prescriptions-list mb-4">
            @forelse($consultation->ordonnance->lignes as $idx => $ligne)
                <div class="drug-item">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <div>
                            <span class="text-muted fw-bold me-2">{{ $idx + 1 }}.</span>
                            <span class="drug-name">{{ $ligne->medicament }}</span>
                            @if($ligne->dosage || $ligne->forme)
                                <span class="drug-dosage">({{ trim($ligne->forme . ' ' . $ligne->dosage) }})</span>
                            @endif
                        </div>
                        @if($ligne->duree)
                            <span class="badge bg-light text-dark border">Pendant {{ $ligne->duree }}</span>
                        @endif
                    </div>
                    <div class="drug-posology">
                        <i data-lucide="arrow-return-right" class="text-primary me-1"></i>
                        <strong>Posologie :</strong> {{ $ligne->posologie }}
                        @if($ligne->instructions)
                            <div class="text-muted small ps-3"><em>Note : {{ $ligne->instructions }}</em></div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted fst-italic">Aucune ligne de médicament enregistrée.</p>
            @endforelse
        </div>

        @if($consultation->ordonnance->instructions_generales)
            <div class="alert alert-light border p-2 small mt-3">
                <strong>Conseils / Recommandations :</strong> {{ $consultation->ordonnance->instructions_generales }}
            </div>
        @endif
    </div>

    {{-- Bas de page & Signature du Médecin --}}
    <div class="signature-section">
        <div class="row align-items-end">
            <div class="col-7 small text-muted">
                <p class="mb-0"><em>Ordonnance valable pour délivrance en pharmacie d'officine.</em></p>
                <p class="mb-0 font-monospace">Consultez en urgence si aggravation des symptômes.</p>
            </div>
            <div class="col-5 text-center">
                <p class="fw-bold mb-5">Signature & Cachet du Médecin</p>
                <div class="border-bottom border-dark w-75 mx-auto"></div>
            </div>
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
