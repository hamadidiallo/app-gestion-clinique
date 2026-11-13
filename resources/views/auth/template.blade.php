<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediGest · Authentification Sécurisée</title>

  {{-- Typographie officielle : Plus Jakarta Sans & Space Grotesk --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Mono:wght@400;600&display=swap" rel="stylesheet">

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html, body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      background: #ffffff;
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      color: #0F1F1A;
      -webkit-font-smoothing: antialiased;
    }
    a { color: #047857; text-decoration: none; }
    a:hover { color: #065F46; }
    ::selection { background: #D1FAE5; color: #065F46; }

    .auth-container {
      min-height: 100vh;
      display: flex;
      width: 100%;
      background: #ffffff;
      overflow-x: clip; /* « clip » et non « hidden » : laisse le panneau droit rester fixe au défilement */
    }

    /* Panneau gauche : Formulaires */
    .form-panel {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 40px 64px;
      min-width: 0;
      background: #ffffff;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .form-inner {
      width: 100%;
      max-width: 440px;
      margin: auto;
      padding: 16px 0;
      min-width: 0;
    }

    /* En-tête de marque MediGest */
    .brand-bar {
      display: flex;
      align-items: center;
      gap: 11px;
      margin-bottom: 30px;
    }
    .brand-icon {
      width: 38px;
      height: 38px;
      border-radius: 12px;
      background: linear-gradient(150deg, #10B981, #047857);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 4px 12px rgba(4, 120, 87, 0.25);
    }
    .brand-title {
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 700;
      font-size: 19px;
      color: #0F1F1A;
      letter-spacing: -0.3px;
    }

    /* Titres & Sous-titres */
    .screen-heading {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 32px;
      font-weight: 700;
      color: #0F1F1A;
      letter-spacing: -0.03em;
      margin: 0 0 8px 0;
      line-height: 1.1;
    }
    .screen-subheading {
      font-size: 15px;
      color: #5B7169;
      font-weight: 500;
      margin: 0 0 28px 0;
      line-height: 1.55;
    }

    /* Parties numérotées d'un formulaire (ordre réel de saisie) */
    .form-section-title {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 0 0 16px 0;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 16px;
      font-weight: 700;
      color: #0F1F1A;
    }
    .form-section-num {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: #D1FAE5;
      color: #065F46;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }
    .form-section + .form-section {
      margin-top: 10px;
      padding-top: 24px;
      border-top: 1px solid #E4EDE9;
    }
    .form-row {
      display: flex;
      gap: 12px;
    }
    .form-row > .form-group {
      flex: 1;
      min-width: 0;
    }
    .form-hint {
      font-size: 12.5px;
      color: #6B7F77;
      font-weight: 500;
      margin-top: 6px;
      line-height: 1.45;
    }
    .form-error {
      font-size: 12.5px;
      color: #DC2626;
      font-weight: 600;
      margin-top: 6px;
    }
    .required-mark { color: #DC2626; }
    .label-facultatif { font-size: 12px; font-weight: 500; color: #8A9B94; margin-left: 4px; }

    /* Écran de création : formulaire large en deux colonnes, tenant sans défilement */
    .form-inner.form-inner-large { max-width: 600px; padding: 0; }
    .form-panel:has(.form-inner-large) { padding-top: 24px; padding-bottom: 24px; }
    .form-panel:has(.form-inner-large) .form-panel-pied { display: none; }
    .form-inner-large .brand-bar { margin-bottom: 16px; }
    .form-compact .btn-back-link { margin-bottom: 12px; }
    .form-compact .screen-heading { font-size: 30px; }
    .form-compact .screen-subheading { font-size: 14.5px; margin-bottom: 20px; }
    .form-compact .form-group { margin-bottom: 12px; }
    .form-compact .form-label { margin-bottom: 5px; font-size: 13px; }
    .form-compact .form-input { min-height: 44px; padding: 9px 14px; }
    .form-hint-cellule { display: flex; align-items: flex-end; padding-bottom: 4px; }
    .form-compact .btn-primary-gradient { margin-top: 8px; }

    /* Rubriques du formulaire, dans le style du ticket thermique imprimé */
    .rubrique-ticket {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 4px 0 12px;
      font-family: 'IBM Plex Mono', 'Courier New', monospace;
      font-size: 12.5px;
      font-weight: 600;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      color: #0F1F1A;
    }
    .rubrique-ticket span { color: #047857; }
    .rubrique-ticket::after {
      content: '';
      flex: 1;
      border-top: 1.5px dashed #C9D8D1;
    }
    .form-row + .rubrique-ticket { margin-top: 10px; }

    .lien-connexion { margin: 14px 0 0; text-align: center; font-size: 14px; color: #5B7169; }
    .lien-connexion button {
      background: none;
      border: 0;
      padding: 4px 2px;
      font: inherit;
      font-weight: 700;
      color: #047857;
      cursor: pointer;
      text-decoration: underline;
      text-underline-offset: 3px;
    }
    .lien-connexion button:hover { color: #065F46; }
    @media (max-width: 560px) {
      .form-compact .form-row { flex-direction: column; gap: 0; }
      .form-hint-cellule { padding-bottom: 0; }
    }

    /* Stepper 1/3, 2/3 */
    .stepper-row {
      display: flex;
      align-items: center;
      gap: 7px;
      margin-bottom: 22px;
    }
    .step-bar {
      height: 4px;
      border-radius: 3px;
      flex: 1;
      background: #E4EDE9;
      transition: background 0.3s;
    }
    .step-bar.active {
      background: linear-gradient(90deg, #10B981, #047857);
    }
    .step-counter {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 11.5px;
      font-weight: 700;
      color: #94A3B8;
      margin-left: 6px;
    }

    /* Bouton retour */
    .btn-back-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 12.5px;
      color: #5B7169;
      font-weight: 600;
      cursor: pointer;
      margin-bottom: 18px;
      background: none;
      border: none;
      padding: 0;
      transition: color 0.15s;
    }
    .btn-back-link:hover {
      color: #0F1F1A;
    }

    /* Form Groups & Inputs */
    .form-group {
      margin-bottom: 18px;
    }
    .form-label {
      display: block;
      font-size: 13.5px;
      font-weight: 600;
      color: #2F443C;
      margin-bottom: 8px;
    }
    .form-label-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 7px;
    }
    .form-input {
      width: 100%;
      min-height: 50px;
      border: 1px solid #E1E9E5;
      border-radius: 12px;
      padding: 12px 15px;
      font-size: 16px; /* 16 px : évite le zoom automatique sur iPhone */
      color: #0F1F1A;
      font-weight: 500;
      font-family: inherit;
      background: #F6F9F7;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s, background-color 0.15s;
    }
    .form-input:hover {
      border-color: #C9D8D1;
    }
    .form-input:focus {
      border-color: #047857;
      box-shadow: 0 0 0 4px rgba(4, 120, 87, 0.12);
      background-color: #ffffff;
    }
    .form-input::placeholder {
      color: #94A3B8;
      font-weight: 500;
    }
    .form-input.is-invalid {
      border-color: #EF4444;
      background-color: #FEF2F2;
    }

    /* Password Wrapper & Toggle */
    .password-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .password-wrapper .form-input {
      padding-right: 42px;
    }
    .password-toggle-btn {
      position: absolute;
      right: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: #94A3B8;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      transition: color 0.15s;
    }
    .password-toggle-btn:hover {
      color: #3F554D;
    }

    /* Bouton Primaire MediGest */
    .btn-primary-gradient {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 9px;
      min-height: 52px;
      background: #047857;
      border-radius: 12px;
      padding: 14px 18px;
      color: #ffffff;
      font-size: 15.5px;
      font-weight: 700;
      font-family: inherit;
      border: none;
      cursor: pointer;
      box-shadow: 0 12px 24px -12px rgba(4, 120, 87, 0.6);
      transition: background-color 0.15s, transform 0.05s;
    }
    .btn-primary-gradient:hover {
      background: #065F46;
    }
    .btn-primary-gradient:focus-visible,
    .btn-back-link:focus-visible,
    .password-toggle-btn:focus-visible {
      outline: 3px solid rgba(4, 120, 87, 0.45);
      outline-offset: 2px;
    }
    .btn-primary-gradient:active {
      transform: scale(0.99);
    }

    /* Cartes de choix M0 */
    .choice-card {
      display: flex;
      align-items: center;
      gap: 14px;
      border: 1.5px solid #E4EDE9;
      background: #ffffff;
      border-radius: 16px;
      padding: 16px;
      margin-bottom: 12px;
      cursor: pointer;
      transition: all 0.15s ease-in-out;
      user-select: none;
    }
    .choice-card:hover {
      border-color: #10B981;
    }
    /* Les cartes de choix sont de vrais boutons (accessibles au clavier) */
    button.choice-card {
      width: 100%;
      text-align: left;
      font: inherit;
      color: inherit;
      padding: 18px;
    }
    .choice-card:focus-visible { outline: 3px solid rgba(4, 120, 87, 0.45); outline-offset: 2px; }
    .choice-card-texte { flex: 1; display: flex; flex-direction: column; gap: 3px; min-width: 0; }
    .choice-card-titre { font-size: 15.5px; font-weight: 700; color: #0F1F1A; }
    .choice-card-desc { font-size: 13px; color: #5B7169; font-weight: 500; line-height: 1.45; }

    /* Vérification : clinique reconnue et rôle attribué */
    .carte-invitation {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border: 1.5px dashed #A7D7C5;
      border-radius: 12px 12px 0 0;
      background: #F0FDF9;
    }
    .carte-invitation-avatar {
      width: 40px; height: 40px; flex: 0 0 auto;
      border-radius: 10px;
      background: #047857; color: #ffffff;
      display: flex; align-items: center; justify-content: center;
      font-family: 'IBM Plex Mono', monospace; font-weight: 600; font-size: 13px;
    }
    .carte-invitation-nom { font-size: 15px; font-weight: 700; color: #0F1F1A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .carte-invitation-ville { font-size: 12.5px; color: #5B7169; }
    .carte-invitation-pill { font-size: 11.5px; font-weight: 700; color: #065F46; background: #D1FAE5; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .carte-role {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 18px;
      padding: 10px 14px;
      border: 1.5px dashed #A7D7C5;
      border-top: 0;
      border-radius: 0 0 12px 12px;
      font-family: 'IBM Plex Mono', monospace;
      font-size: 12.5px;
      color: #3F554D;
    }
    .carte-role strong { color: #047857; font-size: 13.5px; }
    .choice-card.selected {
      border: 1.5px solid #10B981;
      background: #F0FDF9;
    }
    .choice-card-icon {
      width: 46px;
      height: 46px;
      border-radius: 13px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
    }
    .choice-radio-circle {
      width: 22px;
      height: 22px;
      border-radius: 50%;
      border: 2px solid #CBD5D0;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      transition: all 0.15s;
    }
    .choice-card.selected .choice-radio-circle {
      border-color: #047857;
    }
    .choice-radio-dot {
      width: 11px;
      height: 11px;
      border-radius: 50%;
      background: #047857;
      display: none;
    }
    .choice-card.selected .choice-radio-dot {
      display: block;
    }

    /* Cases de Code d'Invitation M0c */
    .code-boxes-grid {
      display: flex;
      gap: 9px;
      margin-bottom: 10px;
      width: 100%;
      max-width: 100%;
      box-sizing: border-box;
    }
    .code-char-box {
      flex: 1 1 0;
      min-width: 0;
      width: 0;
      height: 58px;
      border-radius: 12px;
      border: 1.5px solid #DEE7E2;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Space Grotesk', sans-serif;
      font-size: 22px;
      font-weight: 700;
      color: #0F1F1A;
      text-align: center;
      text-transform: uppercase;
      outline: none;
      padding: 0;
      box-sizing: border-box;
      transition: all 0.15s;
    }
    .code-char-box:focus {
      border-color: #10B981;
      background: #F0FDF9;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }
    .code-char-box.filled {
      border-color: #10B981;
      background: #F0FDF9;
    }

    /* Note d'aide d'invitation bleue */
    .info-notice-blue {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #EFF8FF;
      border: 1px solid #BEE3FF;
      border-radius: 11px;
      padding: 11px 13px;
      margin-bottom: 20px;
      font-size: 11.5px;
      color: #1E40AF;
      font-weight: 500;
      line-height: 1.5;
    }

    /* Alerte Flash */
    .alert-flash {
      border-radius: 11px;
      padding: 12px 14px;
      font-size: 12.5px;
      margin-bottom: 18px;
      display: flex;
      align-items: flex-start;
      gap: 10px;
      line-height: 1.4;
    }
    .alert-flash-success {
      background: #F0FDF4;
      border: 1px solid #BBF7D0;
      color: #166534;
    }
    .alert-flash-error {
      background: #FEF2F2;
      border: 1px solid #FECACA;
      color: #991B1B;
    }

    /* Panneau droit : Showcase Médical Dark Emerald */
    .showcase-panel {
      width: 46%;
      max-width: 760px;
      flex: 0 0 auto;
      align-self: flex-start;
      position: sticky;
      top: 0;
      height: 100vh;
      background-color: #06281F;
      background-image:
        radial-gradient(circle at 80% 10%, rgba(52, 211, 153, 0.22), transparent 45%),
        radial-gradient(circle at 10% 95%, rgba(16, 185, 129, 0.14), transparent 40%),
        linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
      background-size: auto, auto, 40px 40px, 40px 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 56px;
      overflow: hidden;
    }
    .showcase-glow { display: none; }
    .showcase-content {
      position: relative;
      width: 100%;
      max-width: 460px;
      z-index: 1;
    }
    .showcase-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 34px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.03em;
      line-height: 1.12;
      margin-bottom: 14px;
    }
    .showcase-desc {
      font-size: 16px;
      color: #A9C4BA;
      font-weight: 500;
      line-height: 1.6;
      margin-bottom: 32px;
    }

    /* Aperçu du ticket thermique (création de l'établissement) */
    .apercu-legende {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      font-weight: 600;
      color: #6EE7B7;
      margin-bottom: 14px;
    }
    .apercu-legende::before {
      content: '';
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #34D399;
      box-shadow: 0 0 0 4px rgba(52, 211, 153, 0.2);
    }
    .apercu-imprimante {
      height: 14px;
      border-radius: 7px;
      background: #021510;
      box-shadow: inset 0 -2px 0 rgba(255, 255, 255, 0.08);
      position: relative;
      z-index: 2;
    }
    .apercu-ticket {
      margin: -7px 22px 0;
      background: #ffffff;
      color: #111111;
      font-family: 'IBM Plex Mono', 'Courier New', monospace;
      font-size: 12.5px;
      line-height: 1.55;
      padding: 24px 20px 30px;
      -webkit-mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
              mask: conic-gradient(from -45deg at bottom, #0000, #000 1deg 89deg, #0000 90deg) 50% / 16px 100%;
    }
    .apercu-centre { text-align: center; }
    .apercu-nom {
      font-weight: 600;
      font-size: 15px;
      letter-spacing: 0.05em;
      text-transform: uppercase;
      word-break: break-word;
    }
    .apercu-vide { color: #9AA5A0; }
    .apercu-sep { border: 0; border-top: 1px dashed #9AA5A0; margin: 12px 0; }
    .apercu-ligne { display: flex; justify-content: space-between; gap: 12px; }
    .apercu-ligne span:last-child { text-align: right; }
    .apercu-total { font-weight: 600; font-size: 14px; }
    .apercu-pied { margin: 14px 0 0; text-align: center; font-size: 11.5px; }
    @media (max-height: 820px) {
      .apercu-ticket { padding: 18px 18px 24px; }
    }

    /* Le ticket passe à gauche : on lit d'abord l'objet imprimé, puis on remplit le formulaire */
    .showcase-panel { order: -1; }

    /* Connexion */
    .lien-discret {
      background: none;
      border: 0;
      padding: 4px 0;
      font: inherit;
      font-size: 13px;
      font-weight: 700;
      color: #047857;
      cursor: pointer;
    }
    .lien-discret:hover { color: #065F46; text-decoration: underline; text-underline-offset: 3px; }
    .lien-discret:focus-visible { outline: 3px solid rgba(4, 120, 87, 0.45); outline-offset: 2px; border-radius: 4px; }
    .aide-encart {
      margin-top: 10px;
      padding: 12px 14px;
      border-radius: 10px;
      background: #F0FDF9;
      border: 1px dashed #A7D7C5;
      font-size: 13.5px;
      color: #065F46;
      line-height: 1.5;
    }
    .case-poste {
      display: flex;
      align-items: center;
      gap: 10px;
      margin: 4px 0 22px;
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      color: #3F554D;
      font-weight: 500;
    }
    .case-poste input { accent-color: #047857; width: 18px; height: 18px; cursor: pointer; }
    #screen_M1 .rubrique-ticket { margin-bottom: 18px; }

    /* Badges & Cartes Flottantes */
    .card-dark-glass {
      background: rgba(255, 255, 255, 0.06);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 18px;
      padding: 20px;
      backdrop-filter: blur(10px);
    }
    .card-white-showcase {
      background: #ffffff;
      border-radius: 18px;
      padding: 20px;
      box-shadow: 0 18px 40px -18px rgba(0, 0, 0, 0.4);
    }

    @media (max-width: 1100px) {
      .showcase-panel {
        width: 42%;
        padding: 36px;
      }
      .showcase-title { font-size: 28px; }
    }
    @media (max-width: 960px) {
      .auth-container {
        flex-direction: column;
      }
      .showcase-panel {
        display: none !important;
      }
      .form-panel {
        padding: 32px 24px;
      }
    }
  </style>
</head>
<body>

@php
  $requestedTab = strtolower((string) request()->query('tab', ''));
  $activeScreen = $defaultScreen ?? 'M1';

  // Gestion intelligente de la redirection vers l'écran approprié en cas d'erreur de validation
  if (old('action_type') === 'creer' || $errors->has('nom_clinique') || $errors->has('ville')) {
      $activeScreen = 'M0b';
  } elseif (old('action_type') === 'rejoindre' || $errors->has('code_invitation') || $errors->has('role_id')) {
      $activeScreen = $errors->has('code_invitation') ? 'M0c' : 'M0d';
  } elseif ($errors->has('email') && old('action_type') === null) {
      $activeScreen = 'M1';
  } elseif ($requestedTab === 'connexion' || $requestedTab === 'login') {
      $activeScreen = 'M1';
  } elseif ($requestedTab === 'rejoindre' || $requestedTab === 'invitation') {
      $activeScreen = 'M0c';
  } elseif ($requestedTab === 'clinique' || $requestedTab === 'creer') {
      $activeScreen = 'M0b';
  }
@endphp

<div class="auth-container">

  {{-- ======================================================== --}}
  {{-- PANNEAU GAUCHE : PROCESSUS D'AUTHENTIFICATION INTERACTIF   --}}
  {{-- ======================================================== --}}
  <div class="form-panel">
    <div class="form-inner {{ in_array($activeScreen, ['M0b', 'M0d'], true) ? 'form-inner-large' : '' }}">

      {{-- LOGO BRANDING MEDIGEST --}}
      <div class="brand-bar">
        <div class="brand-icon">
          <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 7v10M7 12h10"></path>
          </svg>
        </div>
        <span class="brand-title">MediGest</span>
      </div>

      {{-- NOTIFICATIONS FLASH --}}
      @if(session('alert'))
        <div class="alert-flash alert-flash-success">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M20 6L9 17l-5-5"></path></svg>
          <span>{{ session('alert') }}</span>
        </div>
      @endif

      @if($errors->any())
        <div class="alert-flash alert-flash-error">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          <span>{{ $errors->first() }}</span>
        </div>
      @endif


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0 : BIENVENUE · CHOIX DU TYPE DE COMPTE          --}}
      {{-- ====================================================== --}}
      <div id="screen_M0" class="auth-screen" style="{{ $activeScreen === 'M0' ? '' : 'display: none;' }}">
        <p class="rubrique-ticket"><span>Démarrage</span>Nouveau compte</p>
        <h1 class="screen-heading">Bienvenue sur MediGest</h1>
        <p class="screen-subheading">Comment souhaitez-vous démarrer ?</p>

        <div role="radiogroup" aria-label="Mode de démarrage">
          {{-- Option 1 : Créer un établissement --}}
          <button type="button" class="choice-card selected" id="choice_create" role="radio" aria-checked="true" onclick="selectStartMode('create')">
            <span class="choice-card-icon" style="background: #D1FAE5; color: #047857;" aria-hidden="true">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V9l5-3 5 3M14 20V6l6 3v11M3 20h18"></path></svg>
            </span>
            <span class="choice-card-texte">
              <span class="choice-card-titre">Créer un établissement</span>
              <span class="choice-card-desc">Vous dirigez une clinique ou un cabinet et vous l'installez sur MediGest.</span>
            </span>
            <span class="choice-radio-circle" aria-hidden="true"><span class="choice-radio-dot"></span></span>
          </button>

          {{-- Option 2 : Rejoindre une clinique --}}
          <button type="button" class="choice-card" id="choice_join" role="radio" aria-checked="false" onclick="selectStartMode('join')">
            <span class="choice-card-icon" style="background: #E8EEF6; color: #3F554D;" aria-hidden="true">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"></circle><path d="M5 20c0-4 3-6 7-6s7 2 7 6"></path></svg>
            </span>
            <span class="choice-card-texte">
              <span class="choice-card-titre">Rejoindre une clinique</span>
              <span class="choice-card-desc">Votre clinique utilise déjà MediGest et vous a remis un code d'invitation.</span>
            </span>
            <span class="choice-radio-circle" aria-hidden="true"><span class="choice-radio-dot"></span></span>
          </button>
        </div>

        <button type="button" class="btn-primary-gradient" style="margin-top: 8px;" onclick="goToSelectedStartMode()">
          <span>Continuer</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        </button>

        <p class="lien-connexion">Vous avez déjà un compte ? <button type="button" onclick="navigateToScreen('M1')">Se connecter</button></p>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0b : CRÉER UN ÉTABLISSEMENT · ÉTAPE 1/3           --}}
      {{-- ====================================================== --}}
      <div id="screen_M0b" class="auth-screen form-compact" style="{{ $activeScreen === 'M0b' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Retour</span>
        </button>

        <h2 class="screen-heading">Créer votre clinique</h2>
        <p class="screen-subheading">Vous inviterez votre équipe juste après.</p>

        <form method="POST" action="{{ route('register') }}" id="formCreateClinic">
          @csrf
          <input type="hidden" name="action_type" value="creer">
          <input type="hidden" name="type_etablissement" id="input_type_etablissement" value="{{ old('type_etablissement') }}">

          <h3 class="rubrique-ticket"><span>01</span>Établissement</h3>

          <div class="form-row">
            <div class="form-group">
              <label for="nom_clinique" class="form-label">Nom de l'établissement <span class="required-mark">*</span></label>
              <input type="text" name="nom_clinique" id="nom_clinique" value="{{ old('nom_clinique') }}" placeholder="Clinique Baobab" class="form-input @error('nom_clinique') is-invalid @enderror" required autocomplete="organization" oninput="updateClinicPreview()" />
              @error('nom_clinique')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="ville_clinique" class="form-label">Ville <span class="required-mark">*</span></label>
              <input type="text" name="ville" id="ville_clinique" value="{{ old('ville') }}" placeholder="Bamako" class="form-input @error('ville') is-invalid @enderror" required autocomplete="address-level2" oninput="updateClinicPreview()" />
              @error('ville')<div class="form-error">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="type_etablissement_select" class="form-label">Type <span class="label-facultatif">facultatif</span></label>
              <input type="text" id="type_etablissement_select" value="{{ old('type_etablissement') }}" placeholder="Polyclinique" class="form-input" oninput="document.getElementById('input_type_etablissement').value = this.value; updateClinicPreview()" />
            </div>
            <div class="form-group">
              <label for="telephone_clinique" class="form-label">Téléphone <span class="label-facultatif">facultatif</span></label>
              <input type="tel" name="telephone_clinique" id="telephone_clinique" value="{{ old('telephone_clinique') }}" placeholder="+223 20 22 44 66" class="form-input" inputmode="tel" oninput="updateClinicPreview()" />
            </div>
          </div>

          <h3 class="rubrique-ticket"><span>02</span>Responsable</h3>

          <div class="form-row">
            <div class="form-group">
              <label for="admin_prenom" class="form-label">Prénom <span class="required-mark">*</span></label>
              <input type="text" name="prenom" id="admin_prenom" value="{{ old('prenom') }}" placeholder="Séni" class="form-input @error('prenom') is-invalid @enderror" required autocomplete="given-name" oninput="updateClinicPreview()" />
              @error('prenom')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="admin_nom" class="form-label">Nom <span class="required-mark">*</span></label>
              <input type="text" name="nom" id="admin_nom" value="{{ old('nom') }}" placeholder="Koné" class="form-input @error('nom') is-invalid @enderror" required autocomplete="family-name" oninput="updateClinicPreview()" />
              @error('nom')<div class="form-error">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="admin_telephone" class="form-label">Téléphone</label>
              <input type="tel" name="telephone" id="admin_telephone" value="{{ old('telephone') }}" placeholder="76 00 00 00" inputmode="tel" autocomplete="tel-national" class="form-input @error('telephone') is-invalid @enderror" />
              @error('telephone')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="admin_email" class="form-label">E-mail</label>
              <input type="email" name="email" id="admin_email" value="{{ old('email') }}" placeholder="direction@clinique.ml" autocomplete="email" class="form-input @error('email') is-invalid @enderror" />
              @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="create_admin_password" class="form-label">Mot de passe <span class="required-mark">*</span></label>
              <div class="password-wrapper">
                <input type="password" name="password" id="create_admin_password" placeholder="6 caractères minimum" class="form-input @error('password') is-invalid @enderror" required autocomplete="new-password" />
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('create_admin_password')" aria-label="Afficher ou masquer le mot de passe">
                  <svg id="eye_icon_create_admin_password" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
              </div>
              @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group form-hint-cellule">
              <p class="form-hint" style="margin: 0;">Le téléphone ou l'e-mail, au choix, vous servira à vous connecter.</p>
            </div>
          </div>

          <button type="submit" class="btn-primary-gradient">
            <span>Créer ma clinique</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
          </button>

          <p class="lien-connexion">Déjà inscrit ? <button type="button" onclick="navigateToScreen('M1')">Se connecter</button></p>
        </form>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0c : REJOINDRE · CODE D'INVITATION (1/3)         --}}
      {{-- ====================================================== --}}
      <div id="screen_M0c" class="auth-screen" style="{{ $activeScreen === 'M0c' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Retour</span>
        </button>

        <p class="rubrique-ticket"><span>01</span>Code de la clinique</p>
        <h2 class="screen-heading">Rejoindre une clinique</h2>
        <p class="screen-subheading">Saisissez le code d'invitation remis par votre administrateur.</p>

        <div class="form-group" style="margin-bottom: 14px;">
          <label for="code_box_0" class="form-label">Code de la clinique <span class="required-mark">*</span></label>
          <div class="code-boxes-grid">
            <input type="text" class="code-char-box" id="code_box_0" maxlength="1" oninput="onBoxInput(0, this)" onkeydown="onBoxKeydown(0, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 1 du code" autofocus />
            <input type="text" class="code-char-box" id="code_box_1" maxlength="1" oninput="onBoxInput(1, this)" onkeydown="onBoxKeydown(1, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 2 du code" />
            <input type="text" class="code-char-box" id="code_box_2" maxlength="1" oninput="onBoxInput(2, this)" onkeydown="onBoxKeydown(2, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 3 du code" />
            <input type="text" class="code-char-box" id="code_box_3" maxlength="1" oninput="onBoxInput(3, this)" onkeydown="onBoxKeydown(3, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 4 du code" />
            <input type="text" class="code-char-box" id="code_box_4" maxlength="1" oninput="onBoxInput(4, this)" onkeydown="onBoxKeydown(4, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 5 du code" />
            <input type="text" class="code-char-box" id="code_box_5" maxlength="1" oninput="onBoxInput(5, this)" onkeydown="onBoxKeydown(5, event)" onpaste="onBoxPaste(event)" autocomplete="off" autocapitalize="characters" aria-label="Caractère 6 du code" />
          </div>
          <div class="form-hint">Le code figure dans le message d'invitation envoyé par la clinique (SMS, WhatsApp ou e-mail).</div>
        </div>

        {{-- Résultat de vérification en direct --}}
        <div id="codeStatusMsg" role="status" aria-live="polite" style="font-size: 13px; font-weight: 600; margin-bottom: 14px; min-height: 20px; text-align: center;"></div>

        <button type="button" class="btn-primary-gradient" id="btnVerifyCode" onclick="handleVerifyCode()">
          <span>Vérifier le code</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
        </button>

        <p class="lien-connexion">Vous avez déjà un compte ? <button type="button" onclick="navigateToScreen('M1')">Se connecter</button></p>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0d : REJOINDRE · VÉRIFICATION & PROFIL (2/3)      --}}
      {{-- ====================================================== --}}
      <div id="screen_M0d" class="auth-screen form-compact" style="{{ $activeScreen === 'M0d' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0c')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Modifier le code</span>
        </button>

        <p class="rubrique-ticket"><span>02</span>Votre compte</p>
        <h2 class="screen-heading">Vérifions votre identité</h2>
        <p class="screen-subheading">Confirmez vos coordonnées pour activer votre compte dans la clinique.</p>

        {{-- Clinique reconnue et rôle attribué par elle (non modifiable) --}}
        <div class="carte-invitation">
          <div id="confirmed_clinic_avatar" class="carte-invitation-avatar">CL</div>
          <div style="flex: 1; min-width: 0;">
            <div id="confirmed_clinic_name" class="carte-invitation-nom">Clinique reconnue</div>
            <div id="confirmed_clinic_city" class="carte-invitation-ville">Établissement vérifié</div>
          </div>
          <span id="confirmed_clinic_code_pill" class="carte-invitation-pill">Code validé</span>
        </div>
        <div class="carte-role">
          <span>Rôle attribué par la clinique</span>
          <strong id="confirmed_role_name">Collaborateur</strong>
        </div>

        <form method="POST" action="{{ route('register') }}" id="formJoinClinic">
          @csrf
          <input type="hidden" name="action_type" value="rejoindre">
          <input type="hidden" name="code_invitation" id="code_invitation_final" value="{{ old('code_invitation') }}">
          <input type="hidden" name="role_id" id="assigned_role_id" value="{{ old('role_id', '1') }}">

          <div class="form-row">
            <div class="form-group">
              <label for="join_prenom" class="form-label">Prénom <span class="required-mark">*</span></label>
              <input type="text" name="prenom" id="join_prenom" value="{{ old('prenom') }}" placeholder="Aminata" autocomplete="given-name" class="form-input @error('prenom') is-invalid @enderror" required />
              @error('prenom')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="join_nom" class="form-label">Nom <span class="required-mark">*</span></label>
              <input type="text" name="nom" id="join_nom" value="{{ old('nom') }}" placeholder="Traoré" autocomplete="family-name" class="form-input @error('nom') is-invalid @enderror" required />
              @error('nom')<div class="form-error">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="join_telephone" class="form-label">Téléphone</label>
              <input type="tel" name="telephone" id="join_telephone" value="{{ old('telephone') }}" placeholder="76 00 00 00" inputmode="tel" autocomplete="tel-national" class="form-input @error('telephone') is-invalid @enderror" />
              @error('telephone')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
              <label for="join_email" class="form-label">E-mail</label>
              <input type="email" name="email" id="join_email" value="{{ old('email') }}" placeholder="dr.traore@clinique.ml" autocomplete="email" class="form-input @error('email') is-invalid @enderror" />
              @error('email')<div class="form-error">{{ $message }}</div>@enderror
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="join_user_password" class="form-label">Mot de passe <span class="required-mark">*</span></label>
              <div class="password-wrapper">
                <input type="password" name="password" id="join_user_password" placeholder="6 caractères minimum" autocomplete="new-password" class="form-input @error('password') is-invalid @enderror" required />
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('join_user_password')" aria-label="Afficher ou masquer le mot de passe">
                  <svg id="eye_icon_join_user_password" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
              </div>
              @error('password')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group form-hint-cellule">
              <p class="form-hint" style="margin: 0;">Le téléphone ou l'e-mail, au choix, vous servira à vous connecter.</p>
            </div>
          </div>

          <button type="submit" class="btn-primary-gradient">
            <span>Activer mon compte</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </form>
      </div>



      {{-- ====================================================== --}}
      {{-- ÉCRAN M1 : CONNEXION (REPRODUCTION EXACTE DU DESIGN)     --}}
      {{-- ====================================================== --}}
      <div id="screen_M1" class="auth-screen" style="{{ $activeScreen === 'M1' ? '' : 'display: none;' }}">
        <p class="rubrique-ticket"><span>Accès</span>Espace équipe</p>
        <h1 class="screen-heading">Bon retour parmi nous.</h1>
        <p class="screen-subheading">Connectez-vous pour reprendre là où l'équipe s'est arrêtée.</p>

        <form method="POST" action="{{ route('login') }}" id="formLogin">
          @csrf

          <div class="form-group">
            <label for="login_email" class="form-label">Téléphone ou e-mail</label>
            <input type="text" name="email" id="login_email" value="{{ old('email') }}" placeholder="76 00 00 00" autocomplete="username" class="form-input @error('email') is-invalid @enderror" required autofocus />
            @error('email')<div class="form-error">{{ $message }}</div>@enderror
          </div>

          <div class="form-group">
            <div class="form-label-row">
              <label for="login_password" class="form-label" style="margin-bottom: 0;">Mot de passe</label>
              <button type="button" class="lien-discret" aria-expanded="false" aria-controls="aide_mot_de_passe" onclick="basculerAideMotDePasse(this)">Mot de passe oublié ?</button>
            </div>
            <div class="password-wrapper">
              <input type="password" name="password" id="login_password" placeholder="Votre mot de passe" autocomplete="current-password" class="form-input @error('password') is-invalid @enderror" required />
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('login_password')" aria-label="Afficher ou masquer le mot de passe">
                <svg id="eye_icon_login_password" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
            <div id="aide_mot_de_passe" class="aide-encart" hidden>
              Demandez à l'administrateur de votre clinique de réinitialiser votre mot de passe depuis la page Utilisateurs.
            </div>
          </div>

          <label class="case-poste">
            <input type="checkbox" name="remember" value="1" checked />
            <span>Rester connecté sur ce poste</span>
          </label>

          <button type="submit" class="btn-primary-gradient">
            <span>Se connecter</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </form>

        <p class="lien-connexion">Nouvelle clinique ou nouveau membre ? <button type="button" onclick="navigateToScreen('M0')">Créer ou rejoindre</button></p>
      </div>

    </div>

    {{-- Footer de page panneau gauche --}}
    <div class="form-panel-pied" style="font-size: 11px; color: #94A3B8; text-align: center; margin-top: 24px;">
      MediGest Web · Gestion de clinique polyvalente
    </div>
  </div>


  {{-- ======================================================== --}}
  {{-- PANNEAU DROIT : SHOWCASE CONTEXTUEL DARK EMERALD           --}}
  {{-- ======================================================== --}}
  <div class="showcase-panel">
    {{-- Halo radial émeraude --}}
    <div class="showcase-glow"></div>

    <div class="showcase-content">

      {{-- 1. SHOWCASE POUR CONNEXION (M1) : PÉRIMÈTRE FONCTIONNEL --}}
      <div id="showcase_M1" class="showcase-screen-content" style="{{ $activeScreen === 'M1' ? '' : 'display: none;' }}">
        <div class="showcase-title">Chaque soir, une clôture de caisse nette.</div>
        <div class="showcase-desc">Chaque ticket, chaque paiement et chaque part d'assurance est enregistré au fil de la journée. La clôture s'imprime en un clic.</div>

        <div class="apercu-legende">Exemple de clôture de caisse</div>
        <div aria-hidden="true">
          <div class="apercu-imprimante"></div>
          <div class="apercu-ticket">
            <div class="apercu-centre">
              <div class="apercu-nom">Clôture de caisse</div>
              <div>Guichet 1 · {{ now()->format('d/m/Y') }}</div>
            </div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>Fond d'ouverture</span><span>50 000</span></div>
            <div class="apercu-ligne"><span>Tickets encaissés</span><span>38</span></div>
            <div class="apercu-ligne"><span>Espèces</span><span>412 500</span></div>
            <div class="apercu-ligne"><span>Autres modes</span><span>75 000</span></div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>Part assurances à réclamer</span><span>186 250</span></div>
            <div class="apercu-ligne"><span>Dettes patients</span><span>12 000</span></div>
            <hr class="apercu-sep">
            <div class="apercu-ligne apercu-total"><span>TOTAL ENCAISSÉ</span><span>487 500 F</span></div>
          </div>
        </div>
      </div>

      {{-- 2. SHOWCASE POUR BIENVENUE (M0) : DEUX FAÇONS DE DÉMARRER --}}
      <div id="showcase_M0" class="showcase-screen-content" style="{{ $activeScreen === 'M0' ? '' : 'display: none;' }}">
        <div class="showcase-title">Une plateforme, deux façons de démarrer.</div>
        <div class="showcase-desc">Le gérant ou directeur crée la clinique et invite l'équipe. Chaque membre rejoint ensuite avec le code de la clinique.</div>

        <div class="apercu-legende">Comment ça se passe</div>
        <div aria-hidden="true">
          <div class="apercu-imprimante"></div>
          <div class="apercu-ticket">
            <div class="apercu-centre">
              <div class="apercu-nom">Mise en route</div>
              <div>MediGest · {{ now()->format('d/m/Y') }}</div>
            </div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>1. Gérant / directeur</span><span>crée</span></div>
            <div class="apercu-pied" style="text-align: left; margin: 2px 0 8px;">la clinique et invite l'équipe</div>
            <div class="apercu-ligne"><span>2. Équipe</span><span>rejoint</span></div>
            <div class="apercu-pied" style="text-align: left; margin: 2px 0 8px;">avec le code de la clinique</div>
            <div class="apercu-ligne"><span>3. Chacun</span><span>se connecte</span></div>
            <div class="apercu-pied" style="text-align: left; margin: 2px 0 0;">par téléphone ou e-mail</div>
            <hr class="apercu-sep">
            <div class="apercu-ligne apercu-total"><span>PRÊT À ENCAISSER</span><span>✓</span></div>
          </div>
        </div>
      </div>

      {{-- 3. SHOWCASE POUR CRÉATION (M0b) : CONFIGURÉ EN 3 ÉTAPES --}}
      <div id="showcase_M0b" class="showcase-screen-content" style="{{ $activeScreen === 'M0b' ? '' : 'display: none;' }}">
        <div class="showcase-title">Vos tickets portent déjà votre nom.</div>
        <div class="showcase-desc">Ce que vous saisissez apparaît en tête de chaque ticket et de chaque ordonnance imprimés par votre clinique.</div>

        <div class="apercu-legende">Aperçu en direct</div>
        <div aria-hidden="true">
          <div class="apercu-imprimante"></div>
          <div class="apercu-ticket">
            <div class="apercu-centre">
              <div class="apercu-nom" id="preview_clinic_name" data-vide="Votre établissement">Votre établissement</div>
              <div id="preview_clinic_type" data-vide=""></div>
              <div id="preview_clinic_contact" data-vide="Ville · Téléphone">Ville · Téléphone</div>
            </div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>Ticket</span><span>TCK-{{ now()->format('Ymd') }}-0001</span></div>
            <div class="apercu-ligne"><span>Date</span><span>{{ now()->format('d/m/Y H:i') }}</span></div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>Consultation générale</span><span>10 000</span></div>
            <hr class="apercu-sep">
            <div class="apercu-ligne apercu-total"><span>TOTAL</span><span>10 000 F</span></div>
            <p class="apercu-pied" id="preview_caissier" data-vide="Caissier : votre nom">Caissier : votre nom</p>
          </div>
        </div>
      </div>

      {{-- 4. SHOWCASE POUR CODE D'INVITATION (M0c) : ÉQUIPE & RÔLES --}}
      <div id="showcase_M0c" class="showcase-screen-content" style="{{ $activeScreen === 'M0c' ? '' : 'display: none;' }}">
        <div class="showcase-title">Rejoignez votre équipe en un instant.</div>
        <div class="showcase-desc">Le code relie votre compte à la bonne clinique et applique automatiquement le rôle choisi par votre administrateur.</div>

        <div class="apercu-legende">Exemple d'invitation</div>
        <div aria-hidden="true">
          <div class="apercu-imprimante"></div>
          <div class="apercu-ticket">
            <div class="apercu-centre">
              <div class="apercu-nom">Invitation</div>
              <div>Votre clinique · MediGest</div>
            </div>
            <hr class="apercu-sep">
            <div class="apercu-centre" style="font-size: 22px; font-weight: 600; letter-spacing: 0.35em; margin: 6px 0;">K7M2QA</div>
            <hr class="apercu-sep">
            <div class="apercu-pied" style="margin-top: 0;">Rôles possibles</div>
            <div class="apercu-centre" style="margin-top: 6px;">Médecin · Réceptionniste · Caissier · Comptable</div>
          </div>
        </div>
      </div>

      {{-- 5. SHOWCASE POUR VÉRIFICATION (M0d) : SÉCURITÉ DES DONNÉES --}}
      <div id="showcase_M0d" class="showcase-screen-content" style="{{ $activeScreen === 'M0d' ? '' : 'display: none;' }}">
        <div class="showcase-title">Une vérification, pour protéger les données de santé.</div>
        <div class="showcase-desc">Votre compte n'est activé qu'avec un code valide : vous ne voyez que la clinique qui vous a invité, et seulement ce que votre rôle autorise.</div>

        <div class="apercu-legende">Accès vérifié</div>
        <div aria-hidden="true">
          <div class="apercu-imprimante"></div>
          <div class="apercu-ticket">
            <div class="apercu-centre">
              <div class="apercu-nom">Accès vérifié</div>
              <div>{{ now()->format('d/m/Y H:i') }}</div>
            </div>
            <hr class="apercu-sep">
            <div class="apercu-ligne"><span>Code d'invitation</span><span>valide</span></div>
            <div class="apercu-ligne"><span>Clinique</span><span>reconnue</span></div>
            <div class="apercu-ligne"><span>Rôle</span><span>attribué</span></div>
            <hr class="apercu-sep">
            <div class="apercu-ligne apercu-total"><span>COMPTE</span><span>À ACTIVER</span></div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>

<script>
  let selectedMode = 'create';

  // Basculement visuel des écrans (M0, M0b, M0c, M0d, M1)
  function navigateToScreen(screenId) {
    const screens = ['M0', 'M0b', 'M0c', 'M0d', 'M1'];
    screens.forEach(s => {
      const el = document.getElementById('screen_' + s);
      const sc = document.getElementById('showcase_' + s);
      if (el) el.style.display = (s === screenId) ? 'block' : 'none';
      if (sc) sc.style.display = (s === screenId) ? 'block' : 'none';
    });

    // Le formulaire de création, en deux colonnes, a besoin de plus de largeur
    const formInner = document.querySelector('.form-inner');
    if (formInner) formInner.classList.toggle('form-inner-large', ['M0b', 'M0d'].includes(screenId));

    // Mettre à jour l'URL sans rechargement pour confort UX
    if (window.history && window.history.replaceState) {
      let tabParam = '';
      if (screenId === 'M1') tabParam = 'connexion';
      else if (screenId === 'M0b') tabParam = 'clinique';
      else if (screenId === 'M0c' || screenId === 'M0d') tabParam = 'rejoindre';
      const newUrl = window.location.pathname + (tabParam ? '?tab=' + tabParam : '');
      window.history.replaceState({}, '', newUrl);
    }
  }

  // Sélection du mode de démarrage dans M0
  function selectStartMode(mode) {
    selectedMode = mode;
    const cardCreate = document.getElementById('choice_create');
    const cardJoin = document.getElementById('choice_join');
    if (mode === 'create') {
      cardCreate.classList.add('selected');
      cardJoin.classList.remove('selected');
    } else {
      cardJoin.classList.add('selected');
      cardCreate.classList.remove('selected');
    }
    cardCreate.setAttribute('aria-checked', String(mode === 'create'));
    cardJoin.setAttribute('aria-checked', String(mode !== 'create'));
  }

  function goToSelectedStartMode() {
    if (selectedMode === 'create') {
      navigateToScreen('M0b');
    } else {
      navigateToScreen('M0c');
    }
  }

  // Afficher / Masquer mot de passe
  function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('eye_icon_' + inputId);
    if (!input) return;

    if (input.type === 'password') {
      input.type = 'text';
      if (icon) {
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
      }
    } else {
      input.type = 'password';
      if (icon) {
        icon.innerHTML = '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>';
      }
    }
  }

  // Prévisualisation instantanée du nom de clinique dans M0b
  function updateClinicPreview() {
    const valeur = (id) => ((document.getElementById(id) || {}).value || '').trim();
    const ecrire = (id, texte) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.textContent = texte || el.dataset.vide;
      el.classList.toggle('apercu-vide', !texte);
    };

    ecrire('preview_clinic_name', valeur('nom_clinique'));
    ecrire('preview_clinic_type', valeur('type_etablissement_select'));
    ecrire('preview_clinic_contact', [valeur('ville_clinique'), valeur('telephone_clinique')].filter(Boolean).join(' · '));
    const responsable = [valeur('admin_prenom'), valeur('admin_nom')].filter(Boolean).join(' ');
    ecrire('preview_caissier', responsable ? 'Caissier : ' + responsable : '');
  }

  // Aide « Mot de passe oublié ? » affichée sous le champ, sans fenêtre bloquante
  function basculerAideMotDePasse(bouton) {
    const aide = document.getElementById('aide_mot_de_passe');
    if (!aide) return;
    aide.hidden = !aide.hidden;
    bouton.setAttribute('aria-expanded', String(!aide.hidden));
  }
  updateClinicPreview();

  // Gestion des 6 cases de code d'invitation (M0c)
  const codeHiddenFinal = document.getElementById('code_invitation_final');
  const statusMsg = document.getElementById('codeStatusMsg');
  let codeDebounceTimer = null;

  function getFullBoxCode() {
    let code = '';
    for (let i = 0; i < 6; i++) {
      const b = document.getElementById('code_box_' + i);
      if (b) code += (b.value || '').trim();
    }
    return code.toUpperCase();
  }

  function updateBoxStyles() {
    for (let i = 0; i < 6; i++) {
      const b = document.getElementById('code_box_' + i);
      if (b) {
        if (b.value.trim().length > 0) {
          b.classList.add('filled');
        } else {
          b.classList.remove('filled');
        }
      }
    }
  }

  function populateBoxesFromCode(code) {
    const clean = (code || '').toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
    for (let i = 0; i < 6; i++) {
      const b = document.getElementById('code_box_' + i);
      if (b) {
        b.value = clean[i] || '';
      }
    }
    updateBoxStyles();
    if (codeHiddenFinal) codeHiddenFinal.value = clean;
  }

  function onBoxInput(index, el) {
    const val = (el.value || '').toUpperCase().replace(/[^A-Z0-9]/g, '');
    el.value = val.slice(-1);
    updateBoxStyles();

    const code = getFullBoxCode();
    if (codeHiddenFinal) codeHiddenFinal.value = code;

    // Déplacement automatique vers la case suivante
    if (el.value.length === 1 && index < 5) {
      const next = document.getElementById('code_box_' + (index + 1));
      if (next) {
        next.focus();
        next.select();
      }
    }

    clearTimeout(codeDebounceTimer);
    if (code.length >= 4) {
      codeDebounceTimer = setTimeout(() => {
        checkCodeValidity(code, false);
      }, 300);
    } else {
      if (statusMsg) statusMsg.innerText = '';
    }
  }

  function onBoxKeydown(index, e) {
    if (e.key === 'Backspace') {
      const cur = document.getElementById('code_box_' + index);
      if (cur && cur.value === '' && index > 0) {
        const prev = document.getElementById('code_box_' + (index - 1));
        if (prev) {
          prev.focus();
          prev.value = '';
          updateBoxStyles();
          const code = getFullBoxCode();
          if (codeHiddenFinal) codeHiddenFinal.value = code;
        }
      }
    } else if (e.key === 'ArrowLeft' && index > 0) {
      const prev = document.getElementById('code_box_' + (index - 1));
      if (prev) prev.focus();
    } else if (e.key === 'ArrowRight' && index < 5) {
      const next = document.getElementById('code_box_' + (index + 1));
      if (next) next.focus();
    } else if (e.key === 'Enter') {
      e.preventDefault();
      handleVerifyCode();
    }
  }

  function onBoxPaste(e) {
    e.preventDefault();
    const text = (e.clipboardData || window.clipboardData).getData('text') || '';
    const clean = text.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 6);
    if (!clean) return;

    populateBoxesFromCode(clean);

    const targetIdx = Math.min(clean.length, 5);
    const targetBox = document.getElementById('code_box_' + targetIdx);
    if (targetBox) targetBox.focus();

    if (clean.length >= 4) {
      checkCodeValidity(clean, false);
    }
  }

  // Vérification AJAX du code
  function checkCodeValidity(code, advanceOnSuccess) {
    const clean = (code || '').trim().toUpperCase();
    if (!clean) {
      if (statusMsg) {
        statusMsg.innerText = 'Veuillez saisir votre code d\'invitation.';
        statusMsg.style.color = '#EF4444';
      }
      return;
    }

    if (statusMsg) {
      statusMsg.innerText = 'Vérification du code en cours...';
      statusMsg.style.color = '#5B7169';
    }

    fetch('/auth/verifier-code/' + encodeURIComponent(clean))
      .then(res => res.json())
      .then(data => {
        if (data.valide) {
          const roleLabel = data.role_nom ? ' · Rôle : ' + data.role_nom : '';
          if (statusMsg) {
            statusMsg.innerText = 'Clinique validée : ' + data.nom + roleLabel;
            statusMsg.style.color = '#047857';
          }

          // Mettre à jour la carte et les champs dans M0d
          const nameEl = document.getElementById('confirmed_clinic_name');
          const cityEl = document.getElementById('confirmed_clinic_city');
          const avatarEl = document.getElementById('confirmed_clinic_avatar');
          const codePill = document.getElementById('confirmed_clinic_code_pill');
          const roleEl = document.getElementById('confirmed_role_name');
          const roleInput = document.getElementById('assigned_role_id');
          const prenomInput = document.getElementById('join_prenom');
          const nomInput = document.getElementById('join_nom');
          const emailInput = document.getElementById('join_email');

          if (nameEl) nameEl.innerText = data.nom;
          if (cityEl) cityEl.innerText = (data.ville || 'Mali') + ' · ' + (data.statut || 'Clinique active');
          if (avatarEl) avatarEl.innerText = data.initiales || 'CL';
          if (codePill) codePill.innerText = 'Code ' + clean + ' validé';
          if (codeHiddenFinal) codeHiddenFinal.value = clean;
          if (roleEl && data.role_nom) roleEl.innerText = data.role_nom;
          if (roleInput && data.role_id) roleInput.value = data.role_id;

          // Pré-remplissage sécurisé si l'invitation est nominative
          if (prenomInput && data.prenom) prenomInput.value = data.prenom;
          if (nomInput && data.nom_famille) nomInput.value = data.nom_famille;
          if (emailInput && data.email) emailInput.value = data.email;

          if (advanceOnSuccess) {
            setTimeout(() => {
              navigateToScreen('M0d');
            }, 250);
          }
        } else {
          if (statusMsg) {
            statusMsg.innerText = 'Ce code d\'invitation n\'est pas reconnu.';
            statusMsg.style.color = '#EF4444';
          }
        }
      })
      .catch(() => {
        // En cas d'indisponibilité réseau ponctuelle, permettre de continuer vers le formulaire
        if (advanceOnSuccess) {
          if (codeHiddenFinal) codeHiddenFinal.value = clean;
          navigateToScreen('M0d');
        }
      });
  }

  function handleVerifyCode() {
    const code = getFullBoxCode() || (codeHiddenFinal ? codeHiddenFinal.value.trim() : '');
    checkCodeValidity(code, true);
  }

  // Initialisation à l'ouverture de la page
  document.addEventListener('DOMContentLoaded', () => {
    const initialCode = codeHiddenFinal ? codeHiddenFinal.value : '';
    if (initialCode) {
      populateBoxesFromCode(initialCode);
      checkCodeValidity(initialCode, false);
    }
  });
</script>

</body>
</html>
