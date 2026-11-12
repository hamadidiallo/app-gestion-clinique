<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $ticket->reference }}</title>
    {{-- Aucune ressource externe : la facture s'affiche et s'imprime même avec une connexion faible --}}
    <style>
        @page { size: A4; margin: 14mm 14mm 16mm; }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; }
        body {
            background: #E9EEEC;
            color: #111A16;
            font-family: "Segoe UI", system-ui, -apple-system, Roboto, Arial, sans-serif;
            font-size: 12.5px;
            line-height: 1.45;
        }

        /* Barre d'actions (écran uniquement) */
        .barre-actions {
            position: sticky; top: 0; z-index: 5;
            display: flex; flex-wrap: wrap; gap: 10px; align-items: center; justify-content: space-between;
            padding: 12px 20px; background: #0F1F1A; color: #fff;
        }
        .barre-actions strong { font-size: 15px; }
        .barre-actions span { color: #A9C4BA; font-size: 13px; }
        .barre-boutons { display: flex; flex-wrap: wrap; gap: 8px; }
        .bouton {
            display: inline-flex; align-items: center; min-height: 40px; padding: 0 16px;
            border-radius: 10px; border: 1px solid rgba(255,255,255,.25); background: transparent;
            color: #fff; font: 600 14px/1 inherit; font-family: inherit; text-decoration: none; cursor: pointer;
        }
        .bouton-plein { background: #10B981; border-color: #10B981; color: #062C22; }
        .bouton:focus-visible { outline: 3px solid #6EE7B7; outline-offset: 2px; }

        /* Feuille A4 */
        .feuille {
            width: 210mm; min-height: 297mm; margin: 24px auto; padding: 14mm;
            background: #fff; box-shadow: 0 10px 30px rgba(15, 31, 26, .12);
            display: flex; flex-direction: column;
        }

        .entete { display: flex; justify-content: space-between; gap: 24px; padding-bottom: 16px; border-bottom: 2px solid #047857; }
        .clinique { display: flex; gap: 14px; align-items: flex-start; }
        .clinique img { max-height: 64px; max-width: 120px; object-fit: contain; }
        .clinique-nom { font-size: 19px; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; margin: 0 0 2px; }
        .clinique-infos { color: #4B5F57; font-size: 12px; }
        .document { text-align: right; }
        .document-titre { font-size: 28px; font-weight: 800; letter-spacing: .06em; color: #047857; margin: 0; }
        .document-meta { margin-top: 6px; font-size: 12.5px; }
        .document-meta div { white-space: nowrap; }

        .blocs { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin: 18px 0; }
        .bloc { border: 1px solid #DCE5E0; border-radius: 8px; padding: 10px 12px; }
        .bloc-titre { font-size: 10.5px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #047857; margin: 0 0 6px; }
        .bloc-ligne { display: flex; justify-content: space-between; gap: 12px; }
        .bloc-ligne span:first-child { color: #4B5F57; }
        .bloc-ligne span:last-child { font-weight: 600; text-align: right; }

        table.lignes { width: 100%; border-collapse: collapse; }
        table.lignes th {
            background: #F0F5F3; color: #2F443C; font-size: 11px; text-transform: uppercase; letter-spacing: .06em;
            text-align: left; padding: 8px 10px; border-bottom: 1px solid #C9D8D1;
        }
        table.lignes td { padding: 8px 10px; border-bottom: 1px solid #E4EDE9; vertical-align: top; }
        table.lignes .num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        table.lignes .type { color: #4B5F57; font-size: 11px; text-transform: uppercase; }
        tr { page-break-inside: avoid; }

        .bas { display: grid; grid-template-columns: 1fr 260px; gap: 20px; margin-top: 16px; align-items: start; }
        .totaux { border: 1px solid #DCE5E0; border-radius: 8px; overflow: hidden; }
        .totaux div { display: flex; justify-content: space-between; gap: 12px; padding: 7px 12px; font-variant-numeric: tabular-nums; }
        .totaux div + div { border-top: 1px solid #E4EDE9; }
        .totaux .net { background: #047857; color: #fff; font-size: 14.5px; font-weight: 700; }
        .totaux .reste { color: #B42318; font-weight: 700; }
        .totaux .solde { color: #047857; font-weight: 700; }

        .lettres { margin: 0; padding: 10px 12px; border-left: 3px solid #047857; background: #F6F9F7; }
        .lettres strong { display: block; font-size: 10.5px; letter-spacing: .1em; text-transform: uppercase; color: #047857; margin-bottom: 2px; }

        .paiements { margin-top: 14px; }
        .paiements table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .paiements td, .paiements th { padding: 5px 8px; border-bottom: 1px solid #E4EDE9; text-align: left; }
        .paiements th { font-size: 10.5px; text-transform: uppercase; letter-spacing: .06em; color: #4B5F57; }

        .signatures { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: auto; padding-top: 36px; }
        .signature { border-top: 1px solid #9AA5A0; padding-top: 6px; text-align: center; color: #4B5F57; font-size: 12px; min-height: 70px; }

        .pied { margin-top: 18px; padding-top: 8px; border-top: 1px solid #E4EDE9; text-align: center; color: #6B7F77; font-size: 10.5px; }

        @media print {
            body { background: #fff; }
            .barre-actions { display: none !important; }
            .feuille { width: auto; min-height: calc(297mm - 30mm); margin: 0; padding: 0; box-shadow: none; }
            .totaux .net { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table.lignes th, .lettres { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @media screen and (max-width: 820px) {
            .feuille { width: auto; min-height: 0; margin: 12px; padding: 18px; }
            .entete, .blocs, .bas { display: block; }
            .document { text-align: left; margin-top: 12px; }
            .bloc, .totaux { margin-top: 12px; }
        }
    </style>
</head>
<body>
@php
    $clinique = $ticket->clinique;
    $fcfa = fn ($montant) => number_format((float) $montant, 0, ',', ' ');
    $service = $ticket->service ?? ($ticket->details->first()?->prestation?->service);
    $medecin = $ticket->medecin ?? ($ticket->details->first()?->prestation?->medecin);
    $tauxAssurance = (float) $ticket->montant_total > 0 ? round(((float) $ticket->montant_assurance / (float) $ticket->montant_total) * 100) : 0;
@endphp

<div class="barre-actions">
    <div>
        <strong>Facture A4</strong>
        <span>· {{ $ticket->reference }}</span>
    </div>
    <div class="barre-boutons">
        <button type="button" class="bouton bouton-plein" onclick="window.print()">Imprimer ou enregistrer en PDF</button>
        <a class="bouton" href="{{ route('tickets.print', $ticket) }}">Ticket 80 mm</a>
        <a class="bouton" href="{{ route('tickets.show', $ticket) }}">Retour au ticket</a>
    </div>
</div>

<main class="feuille">
    <header class="entete">
        <div class="clinique">
            @if (! empty($clinique?->logo))
                <img src="{{ asset('storage/'.$clinique->logo) }}" alt="Logo {{ $clinique->nom }}">
            @endif
            <div>
                <p class="clinique-nom">{{ $clinique->nom ?? config('app.name') }}</p>
                <div class="clinique-infos">
                    @if ($clinique?->type_etablissement)<div>{{ $clinique->type_etablissement }}</div>@endif
                    <div>{{ collect([$clinique?->adresse, $clinique?->ville, $clinique?->pays])->filter()->implode(', ') }}</div>
                    <div>{{ collect([$clinique?->telephone ? 'Tél. '.$clinique->telephone : null, $clinique?->email])->filter()->implode(' · ') }}</div>
                </div>
            </div>
        </div>
        <div class="document">
            <p class="document-titre">FACTURE</p>
            <div class="document-meta">
                <div>N° <strong>{{ $ticket->reference }}</strong></div>
                <div>Date : <strong>{{ \Carbon\Carbon::parse($ticket->date_ticket)->format('d/m/Y') }}</strong></div>
                <div>Statut : <strong>{{ $ticket->reste_a_payer > 0 ? 'Partiellement réglée' : 'Réglée' }}</strong></div>
            </div>
        </div>
    </header>

    <section class="blocs">
        <div class="bloc">
            <p class="bloc-titre">Patient</p>
            <div class="bloc-ligne"><span>Nom</span><span>{{ mb_strtoupper($ticket->patient->nom ?? '') }} {{ $ticket->patient->prenom ?? '' }}</span></div>
            @if ($ticket->patient?->reference)
                <div class="bloc-ligne"><span>N° patient</span><span>{{ $ticket->patient->reference }}</span></div>
            @endif
            @if ($ticket->patient?->telephone)
                <div class="bloc-ligne"><span>Téléphone</span><span>{{ $ticket->patient->telephone }}</span></div>
            @endif
        </div>
        <div class="bloc">
            <p class="bloc-titre">Prise en charge</p>
            @if ($service)
                <div class="bloc-ligne"><span>Service</span><span>{{ $service->nom }}</span></div>
            @endif
            @if ($medecin)
                <div class="bloc-ligne"><span>Médecin</span><span>Dr {{ mb_strtoupper($medecin->nom) }} {{ $medecin->prenom }}</span></div>
            @endif
            @if ($ticket->assurance)
                <div class="bloc-ligne"><span>Assurance</span><span>{{ $ticket->assurance->nom }}</span></div>
                @if ($ticket->patient?->numero_assure)
                    <div class="bloc-ligne"><span>N° assuré</span><span>{{ $ticket->patient->numero_assure }}</span></div>
                @endif
            @else
                <div class="bloc-ligne"><span>Assurance</span><span>Aucune</span></div>
            @endif
        </div>
    </section>

    <table class="lignes">
        <thead>
            <tr>
                <th style="width: 50%;">Désignation</th>
                <th class="num">Qté</th>
                <th class="num">Prix unitaire</th>
                <th class="num">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ticket->details as $detail)
                <tr>
                    <td>
                        {{ $detail->libelle }}
                        <div class="type">{{ $detail->type_item ?? 'acte' }}</div>
                    </td>
                    <td class="num">{{ number_format((float) $detail->quantite, 0, ',', ' ') }}</td>
                    <td class="num">{{ $fcfa($detail->prix_unitaire) }}</td>
                    <td class="num">{{ $fcfa($detail->montant_total) }}</td>
                </tr>
            @empty
                <tr>
                    <td>{{ $ticket->description ?: 'Consultation' }}</td>
                    <td class="num">1</td>
                    <td class="num">{{ $fcfa($ticket->montant_total) }}</td>
                    <td class="num">{{ $fcfa($ticket->montant_total) }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <section class="bas">
        <div>
            <p class="lettres">
                <strong>Arrêtée la présente facture à la somme de</strong>
                {{ ucfirst($montantEnLettres) }}{{ $ticket->montant_assurance > 0 ? ', à la charge du patient' : '' }}.
            </p>

            @if ($ticket->paiements->isNotEmpty())
                <div class="paiements">
                    <table>
                        <thead>
                            <tr><th>Règlement</th><th>Date</th><th>Mode</th><th class="num" style="text-align: right;">Montant</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($ticket->paiements as $paiement)
                                <tr>
                                    <td>{{ $paiement->reference }}</td>
                                    <td>{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '' }}</td>
                                    <td>{{ $paiement->modePaiement->nom ?? '' }}</td>
                                    <td style="text-align: right; white-space: nowrap;">{{ $fcfa($paiement->montant_impute) }} FCFA</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="totaux">
            <div><span>Total</span><span>{{ $fcfa($ticket->montant_total) }} FCFA</span></div>
            @if ($ticket->montant_assurance > 0)
                <div>
                    <span>Part assurance{{ $tauxAssurance ? ' ('.$tauxAssurance.' %)' : '' }}</span>
                    <span>− {{ $fcfa($ticket->montant_assurance) }} FCFA</span>
                </div>
            @endif
            <div class="net"><span>Net à payer</span><span>{{ $fcfa($ticket->montant_patient) }} FCFA</span></div>
            <div><span>Déjà réglé</span><span>{{ $fcfa($ticket->montant_paye) }} FCFA</span></div>
            @if ($ticket->reste_a_payer > 0)
                <div class="reste"><span>Reste dû</span><span>{{ $fcfa($ticket->reste_a_payer) }} FCFA</span></div>
            @else
                <div class="solde"><span>Solde</span><span>Réglée</span></div>
            @endif
        </div>
    </section>

    <section class="signatures">
        <div class="signature">Signature du patient</div>
        <div class="signature">Cachet et signature de la caisse<br>{{ $ticket->user->name ?? '' }}</div>
    </section>

    <footer class="pied">
        {{ $clinique->nom ?? config('app.name') }} · Facture n° {{ $ticket->reference }} · Éditée le {{ now()->format('d/m/Y à H:i') }}
    </footer>
</main>
</body>
</html>
