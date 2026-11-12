<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediGest · Logiciel de gestion de clinique</title>
  <meta name="description" content="MediGest gère l'accueil, les consultations, la caisse, les assurances et les honoraires des médecins de votre clinique, avec un accès adapté à chaque poste.">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --encre: #0F1F1A;
      --encre-douce: #4B5F57;
      --emeraude: #047857;
      --emeraude-profond: #064E3B;
      --menthe: #D1FAE5;
      --brume: #F3F7F5;
      --trait: #DCE5E0;
      --papier: #FFFFFF;
      --rayon: 14px;
      --largeur: 1140px;
    }

    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    body {
      margin: 0;
      background: var(--papier);
      color: var(--encre);
      font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
      font-size: 16px;
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }
    img, svg { display: block; }
    a { color: var(--emeraude); }
    :focus-visible { outline: 3px solid var(--emeraude); outline-offset: 3px; border-radius: 6px; }
    ::selection { background: var(--menthe); color: var(--emeraude-profond); }

    .conteneur { width: 100%; max-width: var(--largeur); margin: 0 auto; padding: 0 24px; }

    /* ---------- Titres ---------- */
    .titre-section {
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: clamp(28px, 4vw, 40px);
      line-height: 1.12;
      letter-spacing: -0.02em;
      margin: 0 0 14px;
    }
    .chapeau { color: var(--encre-douce); font-size: 18px; max-width: 620px; margin: 0; }

    /* ---------- Boutons ---------- */
    .bouton {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      min-height: 48px; padding: 0 22px;
      border-radius: 12px; border: 1.5px solid transparent;
      font: 600 15px/1 'Plus Jakarta Sans', sans-serif;
      text-decoration: none; cursor: pointer;
      transition: background-color .15s, border-color .15s, color .15s;
    }
    .bouton-plein { background: var(--emeraude); color: #fff; }
    .bouton-plein:hover { background: var(--emeraude-profond); color: #fff; }
    .bouton-contour { border-color: var(--trait); color: var(--encre); background: var(--papier); }
    .bouton-contour:hover { border-color: var(--emeraude); color: var(--emeraude); }

    /* ---------- En-tête ---------- */
    .entete { position: sticky; top: 0; z-index: 10; background: rgba(255,255,255,.92); backdrop-filter: blur(8px); border-bottom: 1px solid var(--trait); }
    .entete-barre { display: flex; align-items: center; justify-content: space-between; gap: 16px; height: 68px; }
    .marque { display: flex; align-items: center; gap: 10px; text-decoration: none; color: var(--encre); }
    .marque-icone {
      width: 34px; height: 34px; border-radius: 10px;
      background: linear-gradient(150deg, #10B981, var(--emeraude));
      display: grid; place-items: center;
    }
    .marque-nom { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 20px; letter-spacing: -0.01em; }
    .nav { display: flex; align-items: center; gap: 28px; }
    .nav-lien { color: var(--encre-douce); text-decoration: none; font-weight: 500; font-size: 15px; }
    .nav-lien:hover { color: var(--encre); }
    .entete-actions { display: flex; align-items: center; gap: 10px; }
    .entete-actions .bouton { min-height: 40px; padding: 0 16px; font-size: 14px; }

    /* ---------- Hero ---------- */
    .hero { padding: 72px 0 88px; }
    .hero-grille { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 56px; align-items: center; }
    .hero-sur-titre { font-weight: 600; font-size: 14px; color: var(--emeraude); margin: 0 0 18px; }
    .hero-titre {
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: clamp(36px, 5.4vw, 60px);
      line-height: 1.04;
      letter-spacing: -0.035em;
      margin: 0 0 22px;
    }
    .hero-titre em { font-style: normal; color: var(--emeraude); }
    .hero-texte { font-size: 18px; color: var(--encre-douce); max-width: 540px; margin: 0 0 32px; }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; }
    .hero-note { margin: 18px 0 0; font-size: 14px; color: var(--encre-douce); }

    /* ---------- Signature : le ticket thermique ---------- */
    .imprimante {
      position: relative;
      background: var(--brume);
      border-radius: 24px;
      padding: 0 32px 40px;
      display: flex; flex-direction: column; align-items: center;
    }
    .imprimante-fente {
      width: 100%; max-width: 330px; height: 14px; margin-top: 28px;
      background: var(--encre); border-radius: 7px;
      position: relative; z-index: 2;
    }
    .rouleau { width: 100%; max-width: 300px; overflow: hidden; margin-top: -7px; padding-bottom: 14px; }
    .ticket {
      background: var(--papier);
      font-family: 'IBM Plex Mono', 'Courier New', monospace;
      font-size: 12px; line-height: 1.5; color: #111;
      padding: 22px 18px 26px;
      box-shadow: 0 10px 24px rgba(15, 31, 26, .10);
      --dent: 8px;
      -webkit-mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / calc(2 * var(--dent)) 100%;
              mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / calc(2 * var(--dent)) 100%;
      animation: impression 2.6s cubic-bezier(.45, .05, .35, 1) .3s both;
    }
    @keyframes impression {
      from { transform: translateY(-100%); }
      to { transform: translateY(0); }
    }
    .ticket-centre { text-align: center; }
    .ticket-clinique { font-weight: 600; font-size: 14px; letter-spacing: .06em; }
    .ticket-sep { border: 0; border-top: 1px dashed #9AA5A0; margin: 10px 0; }
    .ticket-ligne { display: flex; justify-content: space-between; gap: 12px; }
    .ticket-ligne span:last-child { text-align: right; white-space: nowrap; }
    .ticket-total { font-weight: 600; font-size: 13px; }
    .ticket-net { font-weight: 600; font-size: 15px; }
    .ticket-lettres { margin: 10px 0 0; font-size: 11px; }
    .ticket-tampon {
      margin: 16px auto 0; width: max-content;
      border: 2px solid var(--emeraude); color: var(--emeraude);
      border-radius: 6px; padding: 2px 12px;
      font-weight: 600; font-size: 13px; letter-spacing: .2em;
      transform: rotate(-6deg);
    }
    .imprimante-legende { margin: 6px 0 0; font-size: 13px; color: var(--encre-douce); text-align: center; }

    /* ---------- Parcours patient ---------- */
    .section { padding: 96px 0; scroll-margin-top: 68px; }
    .section-brume { background: var(--brume); }
    .section-entete { margin-bottom: 48px; }
    .parcours { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; counter-reset: etape; }
    .etape { position: relative; padding-top: 26px; border-top: 2px solid var(--trait); counter-increment: etape; }
    .etape::before {
      content: counter(etape);
      position: absolute; top: -15px; left: 0;
      width: 28px; height: 28px; border-radius: 50%;
      background: var(--emeraude); color: #fff;
      font: 600 13px/28px 'IBM Plex Mono', monospace; text-align: center;
    }
    .etape-poste { font-size: 13px; font-weight: 600; color: var(--emeraude); margin: 0 0 4px; }
    .etape-titre { font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; margin: 0 0 10px; letter-spacing: -0.01em; }
    .etape-texte { margin: 0; color: var(--encre-douce); font-size: 15px; }
    .etape-document {
      display: inline-block; margin-top: 14px;
      font: 500 12px/1 'IBM Plex Mono', monospace; color: var(--encre);
      background: var(--brume); border: 1px solid var(--trait); border-radius: 6px; padding: 6px 8px;
    }

    /* ---------- Postes ---------- */
    .postes { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .poste { background: var(--papier); border: 1px solid var(--trait); border-radius: var(--rayon); padding: 24px 22px; }
    .poste-nom { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; margin: 0 0 12px; }
    .poste-liste { margin: 0; padding: 0; list-style: none; font-size: 14px; color: var(--encre-douce); }
    .poste-liste li { padding: 5px 0 5px 18px; position: relative; }
    .poste-liste li::before { content: ''; position: absolute; left: 0; top: 13px; width: 8px; height: 2px; background: var(--emeraude); }
    .poste-ferme { margin-top: 12px; padding-top: 12px; border-top: 1px dashed var(--trait); font-size: 13px; color: var(--encre-douce); }
    .poste-ferme strong { color: var(--encre); font-weight: 600; }

    .garanties { margin-top: 40px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
    .garantie-titre { font-weight: 700; font-size: 16px; margin: 0 0 6px; }
    .garantie-texte { margin: 0; font-size: 15px; color: var(--encre-douce); }

    /* ---------- Tarifs ---------- */
    .periode { display: inline-flex; gap: 4px; padding: 4px; margin-top: 24px; background: var(--brume); border: 1px solid var(--trait); border-radius: 12px; }
    .periode-choix {
      border: 0; background: transparent; cursor: pointer;
      padding: 9px 16px; border-radius: 9px;
      font: 600 14px/1 'Plus Jakarta Sans', sans-serif; color: var(--encre-douce);
    }
    .periode-choix[aria-pressed="true"] { background: var(--papier); color: var(--encre); box-shadow: 0 1px 3px rgba(15,31,26,.12); }
    .periode-remise { color: var(--emeraude); font-weight: 600; }

    .formules { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; align-items: stretch; }
    .formule { display: flex; flex-direction: column; background: var(--papier); border: 1px solid var(--trait); border-radius: 18px; padding: 30px 26px; }
    .formule-vedette { border: 2px solid var(--emeraude); box-shadow: 0 16px 40px rgba(4, 120, 87, .12); }
    .formule-badge { align-self: flex-start; font-size: 12px; font-weight: 600; border-radius: 999px; padding: 4px 10px; background: var(--brume); color: var(--encre-douce); margin-bottom: 16px; }
    .formule-vedette .formule-badge { background: var(--menthe); color: var(--emeraude-profond); }
    .formule-nom { font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 20px; margin: 0 0 6px; letter-spacing: -0.01em; }
    .formule-desc { margin: 0 0 22px; font-size: 14px; color: var(--encre-douce); min-height: 44px; }
    .formule-prix { display: flex; align-items: baseline; gap: 6px; flex-wrap: wrap; margin: 0; }
    .formule-montant { font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 30px; letter-spacing: -0.02em; }
    .formule-unite { font-size: 14px; color: var(--encre-douce); }
    .formule-equivalent { margin: 4px 0 22px; font-size: 13px; color: var(--encre-douce); min-height: 20px; }
    .formule-liste { list-style: none; margin: 0 0 26px; padding: 22px 0 0; border-top: 1px solid var(--trait); font-size: 14px; flex: 1; }
    .formule-liste li { display: flex; gap: 10px; padding: 5px 0; }
    .formule-liste svg { flex-shrink: 0; margin-top: 4px; }
    .formule .bouton { width: 100%; }
    .tarifs-note { margin: 24px 0 0; font-size: 14px; color: var(--encre-douce); }

    /* ---------- Contact ---------- */
    .contact { background: var(--emeraude-profond); color: #fff; border-radius: 24px; padding: 56px 48px; display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; align-items: center; }
    .contact .titre-section { color: #fff; }
    .contact-texte { margin: 0; color: #C7EBDD; font-size: 17px; }
    .contact-canaux { display: flex; flex-direction: column; gap: 12px; }
    .canal {
      display: flex; align-items: center; gap: 14px;
      background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18);
      border-radius: 14px; padding: 16px 18px; color: #fff; text-decoration: none;
      transition: background-color .15s;
    }
    .canal:hover { background: rgba(255,255,255,.14); color: #fff; }
    .canal-libelle { display: block; font-size: 13px; color: #C7EBDD; }
    .canal-valeur { display: block; font-weight: 600; font-size: 16px; }
    .contact .bouton-plein { background: #fff; color: var(--emeraude-profond); }
    .contact .bouton-plein:hover { background: var(--menthe); }

    /* ---------- Pied de page ---------- */
    .pied { padding: 40px 0; border-top: 1px solid var(--trait); margin-top: 96px; }
    .pied-barre { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 14px; color: var(--encre-douce); }
    .pied-barre a { color: var(--encre-douce); }

    /* ---------- Adaptation écrans ---------- */
    @media (max-width: 960px) {
      .nav { display: none; }
      .hero { padding: 48px 0 64px; }
      .hero-grille { grid-template-columns: 1fr; gap: 48px; }
      .parcours { grid-template-columns: repeat(2, 1fr); row-gap: 44px; }
      .formules { grid-template-columns: 1fr; max-width: 480px; }
      .garanties { grid-template-columns: 1fr; gap: 20px; }
      .contact { grid-template-columns: 1fr; padding: 40px 28px; }
      .formule-desc { min-height: 0; }
    }
    @media (max-width: 560px) {
      .conteneur { padding: 0 16px; }
      .entete-actions .bouton-contour { display: none; }
      .section { padding: 72px 0; }
      .parcours { grid-template-columns: 1fr; }
      .imprimante { padding: 0 16px 32px; }
      .hero-actions .bouton { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .ticket { animation: none; }
    }
  </style>
</head>
<body>

@php
  $fcfa = fn (int $montant) => number_format($montant, 0, ',', ' ');
  $aContact = ! empty($contact);
  $whatsapp = $contact['whatsapp'] ?? null;
  $telephone = $contact['telephone'] ?? null;
  $email = $contact['email'] ?? null;
  $messageWhatsapp = rawurlencode("Bonjour, je souhaite une démonstration de MediGest pour ma clinique.");
@endphp

<header class="entete">
  <div class="conteneur entete-barre">
    <a href="{{ route('accueil') }}" class="marque" aria-label="MediGest, accueil">
      <span class="marque-icone" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
      </span>
      <span class="marque-nom">MediGest</span>
    </a>
    <nav class="nav" aria-label="Sections de la page">
      <a class="nav-lien" href="#parcours">Fonctionnalités</a>
      <a class="nav-lien" href="#postes">Équipe</a>
      <a class="nav-lien" href="#tarifs">Tarifs</a>
      @if ($aContact)
        <a class="nav-lien" href="#contact">Contact</a>
      @endif
    </nav>
    <div class="entete-actions">
      <a class="bouton bouton-contour" href="{{ route('login') }}">Se connecter</a>
      <a class="bouton bouton-plein" href="{{ route('register') }}">Créer ma clinique</a>
    </div>
  </div>
</header>

<main>
  {{-- ================= HERO ================= --}}
  <section class="hero">
    <div class="conteneur hero-grille">
      <div>
        <p class="hero-sur-titre">Logiciel de gestion pour cliniques et cabinets médicaux</p>
        <h1 class="hero-titre">Du ticket d'accueil au bordereau d'assurance, <em>toute la clinique</em> dans un seul outil.</h1>
        <p class="hero-texte">
          MediGest enregistre les patients, les consultations et les encaissements, déduit la part des assurances
          et calcule les honoraires des médecins. Chaque membre de l'équipe ne voit que ce qui concerne son poste.
        </p>
        <div class="hero-actions">
          <a class="bouton bouton-plein" href="{{ route('register') }}">Créer ma clinique</a>
          @if ($aContact)
            <a class="bouton bouton-contour" href="#contact">Demander une démonstration</a>
          @else
            <a class="bouton bouton-contour" href="{{ route('login') }}">Se connecter</a>
          @endif
        </div>
        <p class="hero-note">Votre équipe rejoint la clinique avec un code d'invitation, sans configuration de votre part.</p>
      </div>

      <figure class="imprimante" style="margin: 0;">
        <div class="imprimante-fente" aria-hidden="true"></div>
        <div class="rouleau">
          <div class="ticket" role="img" aria-label="Exemple de ticket de caisse : consultation et glycémie pour 12 500 FCFA, dont 8 750 FCFA pris en charge par l'AMO, net à payer 3 750 FCFA, payé.">
            <div class="ticket-centre" aria-hidden="true">
              <div class="ticket-clinique">CLINIQUE BAOBAB</div>
              <div>Bamako · Tél. 20 00 00 00</div>
            </div>
            <hr class="ticket-sep">
            <div aria-hidden="true">
              <div class="ticket-ligne"><span>Ticket</span><span>TCK-20261112-4F2A1</span></div>
              <div class="ticket-ligne"><span>Date</span><span>12/11/2026 09:42</span></div>
              <div class="ticket-ligne"><span>Patient</span><span>TRAORÉ Aminata</span></div>
              <div class="ticket-ligne"><span>Médecin</span><span>Dr DIALLO</span></div>
            </div>
            <hr class="ticket-sep">
            <div aria-hidden="true">
              <div class="ticket-ligne"><span>Consultation générale</span><span>10 000</span></div>
              <div class="ticket-ligne"><span>Glycémie capillaire</span><span>2 500</span></div>
            </div>
            <hr class="ticket-sep">
            <div aria-hidden="true">
              <div class="ticket-ligne ticket-total"><span>TOTAL</span><span>12 500</span></div>
              <div class="ticket-ligne"><span>Part AMO (70 %)</span><span>− 8 750</span></div>
              <div class="ticket-ligne ticket-net"><span>NET À PAYER</span><span>3 750 F</span></div>
              <p class="ticket-lettres">Arrêté à la somme de : trois mille sept cent cinquante francs CFA.</p>
              <div class="ticket-tampon">PAYÉ</div>
            </div>
          </div>
        </div>
        <figcaption class="imprimante-legende">Exemple de ticket imprimé à la caisse, sur imprimante thermique 80 mm.</figcaption>
      </figure>
    </div>
  </section>

  {{-- ================= PARCOURS ================= --}}
  <section class="section section-brume" id="parcours">
    <div class="conteneur">
      <div class="section-entete">
        <h2 class="titre-section">Le passage d'un patient, de l'accueil à la fin du mois</h2>
        <p class="chapeau">Chaque étape reprend les informations de la précédente : rien n'est ressaisi entre l'accueil, le médecin, la caisse et la comptabilité.</p>
      </div>
      <ol class="parcours">
        <li class="etape">
          <p class="etape-poste">Accueil</p>
          <h3 class="etape-titre">Le patient arrive</h3>
          <p class="etape-texte">Retrouvez-le en tapant quelques lettres de son nom ou son numéro, ou créez sa fiche sans quitter l'écran. Choisissez le service, le médecin et les prestations.</p>
          <span class="etape-document">Ticket TCK-…</span>
        </li>
        <li class="etape">
          <p class="etape-poste">Médecin et soins</p>
          <h3 class="etape-titre">La consultation</h3>
          <p class="etape-texte">Constantes (tension, température, glycémie, SpO2), examen, diagnostic et conduite à tenir. L'ordonnance s'imprime depuis la consultation et rejoint le dossier médical.</p>
          <span class="etape-document">Ordonnance ORD-…</span>
        </li>
        <li class="etape">
          <p class="etape-poste">Caisse</p>
          <h3 class="etape-titre">L'encaissement</h3>
          <p class="etape-texte">La part de l'assurance est déduite automatiquement. Ce qui reste impayé devient une dette suivie au nom du patient. Chaque caisse est ouverte et clôturée avec son fond.</p>
          <span class="etape-document">Reçu PAY-…</span>
        </li>
        <li class="etape">
          <p class="etape-poste">Direction et comptabilité</p>
          <h3 class="etape-titre">La fin du mois</h3>
          <p class="etape-texte">Bordereaux à envoyer à chaque assurance, honoraires des médecins calculés selon vos règles de partage, recettes, dépenses et rapports d'activité.</p>
          <span class="etape-document">Bordereau AMO</span>
        </li>
      </ol>
    </div>
  </section>

  {{-- ================= POSTES ================= --}}
  <section class="section" id="postes">
    <div class="conteneur">
      <div class="section-entete">
        <h2 class="titre-section">Chaque poste voit son travail, et rien de plus</h2>
        <p class="chapeau">Les accès suivent le rôle de chacun. Le secret médical est respecté sans réglage particulier.</p>
      </div>
      <div class="postes">
        <article class="poste">
          <h3 class="poste-nom">Accueil</h3>
          <ul class="poste-liste">
            <li>Fiches patients</li>
            <li>Tickets et prestations</li>
            <li>Prise des constantes</li>
          </ul>
          <p class="poste-ferme"><strong>Fermé :</strong> diagnostic, ordonnances, dossier médical</p>
        </article>
        <article class="poste">
          <h3 class="poste-nom">Médecins et soignants</h3>
          <ul class="poste-liste">
            <li>Consultations et constantes</li>
            <li>Dossier médical complet</li>
            <li>Ordonnances et actes</li>
          </ul>
        </article>
        <article class="poste">
          <h3 class="poste-nom">Caisse</h3>
          <ul class="poste-liste">
            <li>Ouverture et clôture de caisse</li>
            <li>Paiements et reçus</li>
            <li>Suivi des dettes</li>
          </ul>
          <p class="poste-ferme"><strong>Fermé :</strong> consultations, dossier médical</p>
        </article>
        <article class="poste">
          <h3 class="poste-nom">Comptabilité</h3>
          <ul class="poste-liste">
            <li>Recettes et dépenses</li>
            <li>Honoraires des médecins</li>
            <li>Bordereaux d'assurance</li>
          </ul>
          <p class="poste-ferme"><strong>Fermé :</strong> consultations, dossier médical</p>
        </article>
        <article class="poste">
          <h3 class="poste-nom">Administration</h3>
          <ul class="poste-liste">
            <li>Utilisateurs et rôles</li>
            <li>Paramètres de la clinique</li>
            <li>Journal d'activité</li>
          </ul>
        </article>
      </div>

      <div class="garanties">
        <div>
          <h3 class="garantie-titre">Les données de votre clinique restent les vôtres</h3>
          <p class="garantie-texte">Chaque clinique a son espace séparé. Aucune autre clinique utilisant MediGest ne peut voir vos patients ni vos chiffres.</p>
        </div>
        <div>
          <h3 class="garantie-titre">L'argent est tracé</h3>
          <p class="garantie-texte">Ouvertures et clôtures de caisse, tickets, paiements, dépenses et honoraires versés : le journal d'activité indique qui l'a fait et quand.</p>
        </div>
        <div>
          <h3 class="garantie-titre">Connexion par e-mail ou par téléphone</h3>
          <p class="garantie-texte">Chacun se connecte avec l'identifiant qu'il utilise au quotidien.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= TARIFS ================= --}}
  <section class="section section-brume" id="tarifs">
    <div class="conteneur">
      <div class="section-entete">
        <h2 class="titre-section">Tarifs</h2>
        <p class="chapeau">Un abonnement par clinique, en francs CFA. Les mises à jour sont incluses dans toutes les formules.</p>
        <div class="periode" role="group" aria-label="Période de facturation">
          <button type="button" class="periode-choix" data-periode="mensuel" aria-pressed="true">Mensuel</button>
          <button type="button" class="periode-choix" data-periode="annuel" aria-pressed="false">Annuel <span class="periode-remise">· 2 mois offerts</span></button>
        </div>
      </div>

      <div class="formules">
        @foreach ($formules as $formule)
          <article class="formule {{ $formule['is_popular'] ? 'formule-vedette' : '' }}">
            <span class="formule-badge">{{ $formule['badge'] }}</span>
            <h3 class="formule-nom">{{ $formule['nom'] }}</h3>
            <p class="formule-desc">{{ $formule['description'] }}</p>
            <p class="formule-prix">
              <span class="formule-montant"
                    data-mensuel="{{ $fcfa($formule['prix_mensuel']) }}"
                    data-annuel="{{ $fcfa($formule['prix_annuel']) }}">{{ $fcfa($formule['prix_mensuel']) }}</span>
              <span class="formule-unite" data-mensuel="FCFA / mois" data-annuel="FCFA / an">FCFA / mois</span>
            </p>
            <p class="formule-equivalent"
               data-mensuel=""
               data-annuel="soit {{ $fcfa((int) round($formule['prix_annuel'] / 12)) }} FCFA par mois"></p>
            <ul class="formule-liste">
              @foreach ($formule['caracteristiques'] as $caracteristique)
                <li>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                  <span>{{ $caracteristique }}</span>
                </li>
              @endforeach
            </ul>
            <a class="bouton {{ $formule['is_popular'] ? 'bouton-plein' : 'bouton-contour' }}" href="{{ route('register') }}">Créer ma clinique</a>
          </article>
        @endforeach
      </div>
      <p class="tarifs-note">Le choix de la formule se fait après la création de la clinique, depuis la page Abonnement.</p>
    </div>
  </section>

  {{-- ================= CONTACT ================= --}}
  <section class="section" id="contact">
    <div class="conteneur">
      <div class="contact">
        <div>
          <h2 class="titre-section">Voyez MediGest avec les cas de votre clinique</h2>
          <p class="contact-texte">
            @if ($aContact)
              Nous vous montrons le parcours complet, de l'accueil d'un patient au bordereau d'assurance, avec vos services et vos tarifs.
            @else
              Créez votre clinique, invitez votre équipe avec son code et commencez à enregistrer vos patients dès aujourd'hui.
            @endif
          </p>
        </div>
        <div class="contact-canaux">
          @if ($whatsapp)
            <a class="canal" href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}?text={{ $messageWhatsapp }}" target="_blank" rel="noopener">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/></svg>
              <span><span class="canal-libelle">Écrire sur WhatsApp</span><span class="canal-valeur">Demander une démonstration</span></span>
            </a>
          @endif
          @if ($telephone)
            <a class="canal" href="tel:{{ preg_replace('/[^\d+]/', '', $telephone) }}">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>
              <span><span class="canal-libelle">Appeler</span><span class="canal-valeur">{{ $telephone }}</span></span>
            </a>
          @endif
          @if ($email)
            <a class="canal" href="mailto:{{ $email }}?subject={{ rawurlencode('Démonstration MediGest') }}">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>
              <span><span class="canal-libelle">Écrire un e-mail</span><span class="canal-valeur">{{ $email }}</span></span>
            </a>
          @endif
          @unless ($aContact)
            <a class="bouton bouton-plein" href="{{ route('register') }}">Créer ma clinique</a>
          @endunless
        </div>
      </div>
    </div>
  </section>
</main>

<footer class="pied">
  <div class="conteneur pied-barre">
    <span>© {{ date('Y') }} MediGest</span>
    <span><a href="{{ route('login') }}">Se connecter</a> · <a href="{{ route('register') }}">Créer ma clinique</a></span>
  </div>
</footer>

<script>
  (function () {
    var choix = document.querySelectorAll('.periode-choix');
    var cibles = document.querySelectorAll('[data-mensuel]');
    choix.forEach(function (bouton) {
      bouton.addEventListener('click', function () {
        var periode = bouton.getAttribute('data-periode');
        choix.forEach(function (b) { b.setAttribute('aria-pressed', b === bouton ? 'true' : 'false'); });
        cibles.forEach(function (el) { el.textContent = el.getAttribute('data-' + periode); });
      });
    });
  })();
</script>
</body>
</html>
