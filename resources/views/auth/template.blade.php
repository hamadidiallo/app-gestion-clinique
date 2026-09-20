<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MediGest · Authentification Sécurisée</title>

  {{-- Typographie officielle : Plus Jakarta Sans & Space Grotesk --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  
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
      overflow-x: hidden;
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
      max-width: 368px;
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
      font-size: 28px;
      font-weight: 700;
      color: #0F1F1A;
      letter-spacing: -1px;
      margin: 0 0 7px 0;
      line-height: 1.2;
    }
    .screen-subheading {
      font-size: 13px;
      color: #5B7169;
      font-weight: 500;
      margin: 0 0 24px 0;
      line-height: 1.5;
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
      margin-bottom: 16px;
    }
    .form-label {
      display: block;
      font-size: 12.5px;
      font-weight: 600;
      color: #3F554D;
      margin-bottom: 7px;
    }
    .form-label-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 7px;
    }
    .form-input {
      width: 100%;
      border: 1px solid #DEE7E2;
      border-radius: 11px;
      padding: 12px 14px;
      font-size: 13.5px;
      color: #0F1F1A;
      font-weight: 600;
      font-family: inherit;
      background: #ffffff;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s, background-color 0.15s;
    }
    .form-input:focus {
      border-color: #10B981;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
      background-color: #F0FDF9;
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
      background: linear-gradient(135deg, #10B981, #047857);
      border-radius: 11px;
      padding: 13px 18px;
      color: #ffffff;
      font-size: 14px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      box-shadow: 0 10px 22px -8px rgba(4, 120, 87, 0.5);
      transition: opacity 0.15s, transform 0.05s;
    }
    .btn-primary-gradient:hover {
      opacity: 0.95;
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
      width: 600px;
      flex: 0 0 auto;
      background: linear-gradient(160deg, #0E241C, #08170F);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 48px;
      position: relative;
      overflow: hidden;
    }
    .showcase-glow {
      position: absolute;
      right: -120px;
      top: -90px;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(16, 185, 129, 0.28), transparent 68%);
      pointer-events: none;
    }
    .showcase-content {
      position: relative;
      width: 100%;
      max-width: 440px;
      z-index: 1;
    }
    .showcase-title {
      font-family: 'Space Grotesk', sans-serif;
      font-size: 24px;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: -0.5px;
      line-height: 1.3;
      margin-bottom: 10px;
    }
    .showcase-desc {
      font-size: 13px;
      color: #9DB4AB;
      font-weight: 500;
      line-height: 1.6;
      margin-bottom: 26px;
    }

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
        width: 480px;
        padding: 36px;
      }
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
    <div class="form-inner">

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
        <h1 class="screen-heading">Bienvenue sur MediGest</h1>
        <p class="screen-subheading">Comment souhaitez-vous démarrer ?</p>

        {{-- Option 1 : Créer un établissement / une clinique --}}
        <div class="choice-card selected" id="choice_create" onclick="selectStartMode('create')">
          <div class="choice-card-icon" style="background: #DCFCE7;">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 20V9l5-3 5 3M14 20V6l6 3v11M3 20h18"></path>
            </svg>
          </div>
          <div style="flex: 1;">
            <div style="font-size: 14.5px; font-weight: 700; color: #0F1F1A;">Créer un établissement</div>
            <div style="font-size: 11.5px; color: #5B7169; font-weight: 500; margin-top: 2px;">Créer une clinique ou un cabinet médical</div>
          </div>
          <div class="choice-radio-circle">
            <div class="choice-radio-dot"></div>
          </div>
        </div>

        {{-- Option 2 : Rejoindre une clinique --}}
        <div class="choice-card" id="choice_join" onclick="selectStartMode('join')">
          <div class="choice-card-icon" style="background: #DBEAFE;">
            <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#5B7169" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="8" r="3.4"></circle>
              <path d="M5 20c0-4 3-6 7-6s7 2 7 6"></path>
            </svg>
          </div>
          <div style="flex: 1;">
            <div style="font-size: 14.5px; font-weight: 700; color: #0F1F1A;">Rejoindre une clinique</div>
            <div style="font-size: 11.5px; color: #5B7169; font-weight: 500; margin-top: 2px;">Vous êtes soignant, invité par un code</div>
          </div>
          <div class="choice-radio-circle">
            <div class="choice-radio-dot"></div>
          </div>
        </div>

        {{-- Bouton Continuer > --}}
        <div style="margin-top: 8px;">
          <button type="button" class="btn-primary-gradient" onclick="goToSelectedStartMode()">
            <span>Continuer</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </div>

        {{-- Lien vers Connexion --}}
        <div style="font-size: 12.5px; color: #5B7169; font-weight: 500; margin-top: 22px; text-align: center;">
          Vous avez déjà un compte ? <a href="javascript:void(0)" onclick="navigateToScreen('M1')" style="color: #047857; font-weight: 700;">Se connecter</a>
        </div>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0b : CRÉER UN ÉTABLISSEMENT · ÉTAPE 1/3           --}}
      {{-- ====================================================== --}}
      <div id="screen_M0b" class="auth-screen" style="{{ $activeScreen === 'M0b' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Retour au choix de démarrage</span>
        </button>

        {{-- Stepper 1/3 --}}
        <div class="stepper-row">
          <div class="step-bar active"></div>
          <div class="step-bar"></div>
          <div class="step-bar"></div>
          <span class="step-counter">1/3</span>
        </div>

        <h2 class="screen-heading" style="font-size: 27px;">Votre établissement</h2>
        <p class="screen-subheading">Ces informations figureront sur vos ordonnances et factures.</p>

        <form method="POST" action="{{ route('register') }}" id="formCreateClinic">
          @csrf
          <input type="hidden" name="action_type" value="creer">
          <input type="hidden" name="type_etablissement" id="input_type_etablissement" value="Clinique polyvalente">

          <div class="form-group">
            <label for="nom_clinique" class="form-label">Nom de l'établissement <span style="color: #ef4444;">*</span></label>
            <input type="text" name="nom_clinique" id="nom_clinique" value="{{ old('nom_clinique', 'Clinique Baobab') }}" placeholder="Clinique Baobab" class="form-input @error('nom_clinique') is-invalid @enderror" required oninput="updateClinicPreview(this.value)" />
          </div>

          <div style="display: flex; gap: 12px;">
            <div style="flex: 1; min-width: 0;" class="form-group">
              <label for="type_etablissement_select" class="form-label">Type</label>
              <input type="text" id="type_etablissement_select" value="Clinique polyvalente" class="form-input" oninput="document.getElementById('input_type_etablissement').value = this.value" />
            </div>
            <div style="flex: 1; min-width: 0;" class="form-group">
              <label for="ville_clinique" class="form-label">Ville <span style="color: #ef4444;">*</span></label>
              <input type="text" name="ville" id="ville_clinique" value="{{ old('ville', 'Bamako') }}" placeholder="Bamako" class="form-input @error('ville') is-invalid @enderror" required />
            </div>
          </div>

          <div class="form-group">
            <label for="telephone_clinique" class="form-label">Téléphone</label>
            <input type="text" name="telephone_clinique" id="telephone_clinique" value="{{ old('telephone_clinique', '+223 20 22 44 66') }}" placeholder="+223 20 22 44 66" class="form-input" />
          </div>

          {{-- Compte administrateur direct --}}
          <div style="padding-top: 8px; border-top: 1px solid #E4EDE9; margin-top: 6px; margin-bottom: 14px;">
            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #5B7169; letter-spacing: 0.05em; margin-bottom: 10px;">Compte administrateur</div>
            <div style="display: flex; gap: 10px;">
              <div style="flex: 1; min-width: 0;" class="form-group">
                <label class="form-label">Prénom <span style="color: #ef4444;">*</span></label>
                <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Dr Séni" class="form-input" required />
              </div>
              <div style="flex: 1; min-width: 0;" class="form-group">
                <label class="form-label">Nom <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Koné" class="form-input" required />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">E-mail administrateur <span style="color: #ef4444;">*</span></label>
              <input type="email" name="email" value="{{ old('email') }}" placeholder="direction@clinique.ml" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Mot de passe <span style="color: #ef4444;">*</span></label>
              <div class="password-wrapper">
                <input type="password" name="password" id="create_admin_password" placeholder="6 caractères minimum" class="form-input" required />
                <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('create_admin_password')" aria-label="Afficher/masquer">
                  <svg id="eye_icon_create_admin_password" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </button>
              </div>
            </div>
          </div>

          <button type="submit" class="btn-primary-gradient">
            <span>Créer l'établissement</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </form>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0c : REJOINDRE · CODE D'INVITATION (1/3)         --}}
      {{-- ====================================================== --}}
      <div id="screen_M0c" class="auth-screen" style="{{ $activeScreen === 'M0c' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Retour au choix de démarrage</span>
        </button>

        {{-- Stepper 1/3 --}}
        <div class="stepper-row">
          <div class="step-bar active"></div>
          <div class="step-bar"></div>
          <div class="step-bar"></div>
          <span class="step-counter">1/3</span>
        </div>

        <h2 class="screen-heading" style="font-size: 27px;">Rejoindre une clinique</h2>
        <p class="screen-subheading">Saisissez le code d'invitation remis par votre administrateur.</p>

        {{-- Formulaire étape code (6 cases exactes conformes à la maquette M0c) --}}
        <div class="form-group" style="margin-bottom: 16px;">
          <div style="font-size: 12.5px; font-weight: 600; color: #3F554D; margin-bottom: 9px;">Code de la clinique <span style="color: #ef4444;">*</span></div>
          <div class="code-boxes-grid">
            <input type="text" class="code-char-box" id="code_box_0" maxlength="1" oninput="onBoxInput(0, this)" onkeydown="onBoxKeydown(0, event)" onpaste="onBoxPaste(event)" autocomplete="off" autofocus />
            <input type="text" class="code-char-box" id="code_box_1" maxlength="1" oninput="onBoxInput(1, this)" onkeydown="onBoxKeydown(1, event)" onpaste="onBoxPaste(event)" autocomplete="off" />
            <input type="text" class="code-char-box" id="code_box_2" maxlength="1" oninput="onBoxInput(2, this)" onkeydown="onBoxKeydown(2, event)" onpaste="onBoxPaste(event)" autocomplete="off" />
            <input type="text" class="code-char-box" id="code_box_3" maxlength="1" oninput="onBoxInput(3, this)" onkeydown="onBoxKeydown(3, event)" onpaste="onBoxPaste(event)" autocomplete="off" />
            <input type="text" class="code-char-box" id="code_box_4" maxlength="1" oninput="onBoxInput(4, this)" onkeydown="onBoxKeydown(4, event)" onpaste="onBoxPaste(event)" autocomplete="off" />
            <input type="text" class="code-char-box" id="code_box_5" maxlength="1" oninput="onBoxInput(5, this)" onkeydown="onBoxKeydown(5, event)" onpaste="onBoxPaste(event)" autocomplete="off" />
          </div>
        </div>

        {{-- Notice bleue officielle --}}
        <div class="info-notice-blue">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex: 0 0 auto;">
            <circle cx="12" cy="12" r="9"></circle>
            <path d="M12 11v5M12 8h.01"></path>
          </svg>
          <span>Le code figure dans l'e-mail ou le SMS d'invitation envoyé par la clinique.</span>
        </div>

        {{-- Résultat de vérification en direct --}}
        <div id="codeStatusMsg" style="font-size: 12px; font-weight: 600; margin-bottom: 14px; min-height: 18px; text-align: center;"></div>

        {{-- Bouton Vérifier le code > --}}
        <button type="button" class="btn-primary-gradient" id="btnVerifyCode" onclick="handleVerifyCode()">
          <span>Vérifier le code</span>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
        </button>
      </div>


      {{-- ====================================================== --}}
      {{-- ÉCRAN M0d : REJOINDRE · VÉRIFICATION & PROFIL (2/3)      --}}
      {{-- ====================================================== --}}
      <div id="screen_M0d" class="auth-screen" style="{{ $activeScreen === 'M0d' ? '' : 'display: none;' }}">
        <button type="button" class="btn-back-link" onclick="navigateToScreen('M0c')">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="m15 18-6-6 6-6"></path></svg>
          <span>Modifier le code d'invitation</span>
        </button>

        {{-- Stepper 2/3 --}}
        <div class="stepper-row">
          <div class="step-bar active"></div>
          <div class="step-bar active"></div>
          <div class="step-bar"></div>
          <span class="step-counter">2/3</span>
        </div>

        <h2 class="screen-heading" style="font-size: 27px;">Vérifions votre identité</h2>
        <p class="screen-subheading">Confirmez vos coordonnées pour activer votre compte collaborateur.</p>

        {{-- Carte profil clinique validée --}}
        <div style="display: flex; align-items: center; gap: 12px; background: #F7FAF9; border: 1px solid #E7EFEB; border-radius: 12px; padding: 13px 14px; margin-bottom: 18px;">
          <div id="confirmed_clinic_avatar" style="width: 40px; height: 40px; border-radius: 11px; background: #DBEAFE; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 13px; color: #2563EB;">
            CL
          </div>
          <div style="flex: 1; min-width: 0;">
            <div id="confirmed_clinic_name" style="font-size: 13px; font-weight: 700; color: #0F1F1A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Clinique reconnue</div>
            <div id="confirmed_clinic_city" style="font-size: 11px; color: #94A3B8; font-weight: 600;">Établissement vérifié</div>
          </div>
          <span id="confirmed_clinic_code_pill" style="font-size: 10.5px; font-weight: 700; color: #175CD3; background: #EFF8FF; padding: 4px 10px; border-radius: 20px;">Code validé</span>
        </div>

        <form method="POST" action="{{ route('register') }}" id="formJoinClinic">
          @csrf
          <input type="hidden" name="action_type" value="rejoindre">
          <input type="hidden" name="code_invitation" id="code_invitation_final" value="{{ old('code_invitation') }}">

          {{-- Profil attribué par la clinique (Sécurisé & Non modifiable par l'utilisateur) --}}
          <div class="form-group">
            <label class="form-label">Rôle attribué par l'établissement</label>
            <div style="display: flex; align-items: center; justify-content: space-between; background: #F0FDF9; border: 1.5px solid #10B981; border-radius: 11px; padding: 11px 13px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #DCFCE7; display: flex; align-items: center; justify-content: center; color: #047857;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                  <div id="confirmed_role_name" style="font-size: 13.5px; font-weight: 700; color: #0F1F1A;">Collaborateur</div>
                  <div style="font-size: 11px; color: #5B7169;">Rôle médical vérifié par la clinique</div>
                </div>
              </div>
              <span style="font-size: 10.5px; font-weight: 700; color: #047857; background: #DCFCE7; padding: 3px 10px; border-radius: 20px;">Sécurisé</span>
            </div>
            <input type="hidden" name="role_id" id="assigned_role_id" value="{{ old('role_id', '1') }}">
          </div>

          <div style="display: flex; gap: 10px;">
            <div style="flex: 1; min-width: 0;" class="form-group">
              <label class="form-label">Prénom <span style="color: #ef4444;">*</span></label>
              <input type="text" name="prenom" id="join_prenom" value="{{ old('prenom') }}" placeholder="Aminata" class="form-input @error('prenom') is-invalid @enderror" required />
            </div>
            <div style="flex: 1; min-width: 0;" class="form-group">
              <label class="form-label">Nom <span style="color: #ef4444;">*</span></label>
              <input type="text" name="nom" id="join_nom" value="{{ old('nom') }}" placeholder="Traoré" class="form-input @error('nom') is-invalid @enderror" required />
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Adresse e-mail professionnelle <span style="color: #ef4444;">*</span></label>
            <input type="email" name="email" id="join_email" value="{{ old('email') }}" placeholder="dr.traore@clinique.ml" class="form-input @error('email') is-invalid @enderror" required />
          </div>

          <div class="form-group">
            <label class="form-label">Mot de passe de connexion <span style="color: #ef4444;">*</span></label>
            <div class="password-wrapper">
              <input type="password" name="password" id="join_user_password" placeholder="6 caractères minimum" class="form-input @error('password') is-invalid @enderror" required />
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('join_user_password')" aria-label="Afficher/masquer">
                <svg id="eye_icon_join_user_password" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-primary-gradient" style="margin-top: 6px;">
            <span>Activer mon compte</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </form>
      </div>



      {{-- ====================================================== --}}
      {{-- ÉCRAN M1 : CONNEXION (REPRODUCTION EXACTE DU DESIGN)     --}}
      {{-- ====================================================== --}}
      <div id="screen_M1" class="auth-screen" style="{{ $activeScreen === 'M1' ? '' : 'display: none;' }}">
        <h1 class="screen-heading">Connexion</h1>
        <p class="screen-subheading">Accédez à l'espace de la Clinique Baobab.</p>

        <form method="POST" action="{{ route('login') }}" id="formLogin">
          @csrf
          
          {{-- Identifiant professionnel --}}
          <div class="form-group">
            <label for="login_email" class="form-label">Identifiant professionnel</label>
            <input type="text" name="email" id="login_email" value="{{ old('email') }}" placeholder="dr.kone" class="form-input @error('email') is-invalid @enderror" required autofocus />
          </div>

          {{-- Mot de passe avec lien Oublié ? --}}
          <div class="form-group">
            <div class="form-label-row">
              <span class="form-label" style="margin-bottom: 0;">Mot de passe</span>
              <a href="javascript:void(0)" onclick="alert('Veuillez contacter l\'administrateur de votre établissement pour réinitialiser votre accès.')" style="font-size: 12px; font-weight: 700; color: #047857;">Oublié ?</a>
            </div>
            <div class="password-wrapper">
              <input type="password" name="password" id="login_password" placeholder="••••••••" class="form-input @error('password') is-invalid @enderror" required />
              <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('login_password')" aria-label="Afficher ou masquer le mot de passe">
                <svg id="eye_icon_login_password" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </button>
            </div>
          </div>

          {{-- Checkbox personnalisée : Rester connecté sur ce poste --}}
          <label style="display: flex; align-items: center; gap: 9px; margin-bottom: 20px; cursor: pointer; user-select: none;">
            <input type="checkbox" name="remember" value="1" checked style="accent-color: #059669; width: 17px; height: 17px; border-radius: 5px; cursor: pointer;" />
            <span style="font-size: 12.5px; color: #3F554D; font-weight: 500;">Rester connecté sur ce poste</span>
          </label>

          {{-- Bouton Se connecter > --}}
          <button type="submit" class="btn-primary-gradient">
            <span>Se connecter</span>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
          </button>
        </form>

        {{-- Mention de sécurité avec cadenas --}}
        <div style="font-size: 11.5px; color: #94A3B8; font-weight: 500; margin-top: 24px; display: flex; align-items: center; gap: 7px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2.5"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
          <span>Connexion sécurisée · données de santé chiffrées</span>
        </div>

        {{-- Lien vers Créer ou Rejoindre --}}
        <div style="font-size: 12.5px; color: #5B7169; font-weight: 500; margin-top: 20px; text-align: center;">
          Pas encore de compte ? <a href="javascript:void(0)" onclick="navigateToScreen('M0')" style="color: #047857; font-weight: 700;">Créer ou rejoindre</a>
        </div>
      </div>

    </div>

    {{-- Footer de page panneau gauche --}}
    <div style="font-size: 11px; color: #94A3B8; text-align: center; margin-top: 24px;">
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

      {{-- 1. SHOWCASE POUR CONNEXION (M1) : AWA KONÉ & VITALS --}}
      <div id="showcase_M1" class="showcase-screen-content" style="{{ $activeScreen === 'M1' ? '' : 'display: none;' }}">
        <div class="showcase-title">Le dossier patient, du premier rendez-vous à la sortie.</div>
        <div class="showcase-desc">Rendez-vous, consultations, ordonnances, pharmacie et facturation réunis sur un poste unique et sécurisé.</div>

        <div class="card-dark-glass">
          <div style="display: flex; align-items: center; gap: 11px; margin-bottom: 16px;">
            <div style="width: 40px; height: 40px; border-radius: 11px; background: #DBEAFE; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 13px; color: #2563EB;">AK</div>
            <div style="flex: 1;">
              <div style="font-size: 13px; font-weight: 700; color: #ffffff;">Awa Koné · 34 ans</div>
              <div style="font-size: 11px; color: #7E9B90; font-weight: 600;">Consultation générale · 09:30</div>
            </div>
            <span style="font-size: 10px; font-weight: 700; color: #6EE7B7; background: rgba(16, 185, 129, 0.16); padding: 4px 10px; border-radius: 20px;">En salle</span>
          </div>
          <div style="display: flex; gap: 10px;">
            <div style="flex: 1; background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 12px 13px;">
              <div style="font-size: 10px; color: #7E9B90; font-weight: 600;">Tension</div>
              <div style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #ffffff; margin-top: 2px;">12/8</div>
            </div>
            <div style="flex: 1; background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 12px 13px;">
              <div style="font-size: 10px; color: #7E9B90; font-weight: 600;">Temp.</div>
              <div style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #ffffff; margin-top: 2px;">37,2°</div>
            </div>
            <div style="flex: 1; background: rgba(255, 255, 255, 0.05); border-radius: 12px; padding: 12px 13px;">
              <div style="font-size: 10px; color: #7E9B90; font-weight: 600;">Pouls</div>
              <div style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #ffffff; margin-top: 2px;">78</div>
            </div>
          </div>
        </div>
      </div>

      {{-- 2. SHOWCASE POUR BIENVENUE (M0) : DEUX FAÇONS DE DÉMARRER --}}
      <div id="showcase_M0" class="showcase-screen-content" style="{{ $activeScreen === 'M0' ? '' : 'display: none;' }}">
        <div class="showcase-title">Une plateforme, deux façons de démarrer.</div>
        <div class="showcase-desc">Créez l'espace de votre établissement, ou rejoignez celui de votre équipe en un code.</div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 16px; display: flex; align-items: center; gap: 13px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(16, 185, 129, 0.16); display: flex; align-items: center; justify-content: center;">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#6EE7B7" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V9l5-3 5 3M14 20V6l6 3v11"></path></svg>
            </div>
            <div>
              <div style="font-size: 13px; font-weight: 700; color: #ffffff;">Gérant / directeur</div>
              <div style="font-size: 11px; color: #7E9B90; font-weight: 600;">Crée l'établissement &amp; invite l'équipe</div>
            </div>
          </div>
          <div style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 16px; padding: 16px; display: flex; align-items: center; gap: 13px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(37, 99, 235, 0.2); display: flex; align-items: center; justify-content: center;">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#93C5FD" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.4"></circle><path d="M5 20c0-4 3-6 7-6s7 2 7 6"></path></svg>
            </div>
            <div>
              <div style="font-size: 13px; font-weight: 700; color: #ffffff;">Médecin · infirmier · caissier</div>
              <div style="font-size: 11px; color: #7E9B90; font-weight: 600;">Rejoint avec le code de la clinique</div>
            </div>
          </div>
        </div>
      </div>

      {{-- 3. SHOWCASE POUR CRÉATION (M0b) : CONFIGURÉ EN 3 ÉTAPES --}}
      <div id="showcase_M0b" class="showcase-screen-content" style="{{ $activeScreen === 'M0b' ? '' : 'display: none;' }}">
        <div class="showcase-title">Configuré en trois étapes.</div>
        <div class="showcase-desc">Identité de l'établissement, compte administrateur, puis invitation de votre équipe.</div>

        <div class="card-white-showcase">
          <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div style="width: 46px; height: 46px; border-radius: 13px; background: linear-gradient(150deg, #10B981, #047857); display: flex; align-items: center; justify-content: center;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V9l5-3 5 3M14 20V6l6 3v11M3 20h18"></path></svg>
            </div>
            <div>
              <div id="preview_clinic_name" style="font-size: 15px; font-weight: 700; color: #0F1F1A; font-family: 'Space Grotesk', sans-serif;">Clinique Baobab</div>
              <div style="font-size: 11.5px; color: #94A3B8; font-weight: 600;">Clinique polyvalente · Bamako</div>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 9px; padding: 8px 0; border-top: 1px solid #F1F5F3;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#12B76A" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
            <span style="font-size: 12.5px; color: #0F1F1A; font-weight: 600;">Dossiers patients illimités</span>
          </div>
          <div style="display: flex; align-items: center; gap: 9px; padding: 8px 0; border-top: 1px solid #F1F5F3;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#12B76A" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
            <span style="font-size: 12.5px; color: #0F1F1A; font-weight: 600;">Ordonnances &amp; facturation</span>
          </div>
          <div style="display: flex; align-items: center; gap: 9px; padding: 8px 0; border-top: 1px solid #F1F5F3;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#12B76A" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
            <span style="font-size: 12.5px; color: #0F1F1A; font-weight: 600;">Multi-utilisateurs par rôle</span>
          </div>
        </div>
      </div>

      {{-- 4. SHOWCASE POUR CODE D'INVITATION (M0c) : ÉQUIPE & RÔLES --}}
      <div id="showcase_M0c" class="showcase-screen-content" style="{{ $activeScreen === 'M0c' ? '' : 'display: none;' }}">
        <div class="showcase-title">Rejoignez votre équipe en un instant.</div>
        <div class="showcase-desc">Le code relie votre compte à la bonne clinique et applique automatiquement votre rôle.</div>

        <div class="card-white-showcase">
          <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 13px; background: linear-gradient(150deg, #10B981, #047857); display: flex; align-items: center; justify-content: center;">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 7v10M7 12h10"></path></svg>
            </div>
            <div>
              <div style="font-size: 15px; font-weight: 700; color: #0F1F1A; font-family: 'Space Grotesk', sans-serif;">Clinique Baobab</div>
              <div style="font-size: 11.5px; color: #94A3B8; font-weight: 600;">Marché Dabanani, Bamako</div>
            </div>
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <span style="font-size: 10.5px; font-weight: 700; color: #175CD3; background: #EFF8FF; padding: 5px 11px; border-radius: 20px;">Médecin</span>
            <span style="font-size: 10.5px; font-weight: 700; color: #0E7490; background: #ECFEFF; padding: 5px 11px; border-radius: 20px;">Infirmier</span>
            <span style="font-size: 10.5px; font-weight: 700; color: #B45309; background: #FFFAEB; padding: 5px 11px; border-radius: 20px;">Caissier</span>
            <span style="font-size: 10.5px; font-weight: 700; color: #7C3AED; background: #F4F3FF; padding: 5px 11px; border-radius: 20px;">Laborantin</span>
          </div>
        </div>
      </div>

      {{-- 5. SHOWCASE POUR VÉRIFICATION (M0d) : SÉCURITÉ DES DONNÉES --}}
      <div id="showcase_M0d" class="showcase-screen-content" style="{{ $activeScreen === 'M0d' ? '' : 'display: none;' }}">
        <div class="showcase-title">Une vérification, pour la sécurité des données de santé.</div>
        <div class="showcase-desc">Nous confirmons votre identité via le contact que la clinique a enregistré sur votre fiche.</div>

        <div class="card-white-showcase" style="text-align: center; padding: 26px 20px;">
          <div style="width: 60px; height: 60px; border-radius: 50%; background: #DCFCE7; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#047857" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2 4 6v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V6Z"></path>
              <path d="m9 12 2 2 4-4"></path>
            </svg>
          </div>
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #0F1F1A;">Données chiffrées</div>
          <div style="font-size: 12px; color: #94A3B8; font-weight: 500; line-height: 1.6; margin-top: 6px;">Vos accès sont protégés par vérification médicale sécurisée.</div>
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
  function updateClinicPreview(name) {
    const preview = document.getElementById('preview_clinic_name');
    if (preview) {
      preview.innerText = name.trim() || 'Clinique Baobab';
    }
  }

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
