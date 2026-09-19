<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sabati · Gestion de clinique</title>

  {{-- Typographie IBM Plex Sans & IBM Plex Mono --}}
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
  
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      background: #eef1f4;
      font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
      color: #1e2a32;
      -webkit-font-smoothing: antialiased;
    }
    a { color: #0f766e; text-decoration: none; }
    a:hover { color: #0b5a54; text-decoration: underline; }
    ::selection { background: #cfeae6; }
    input::placeholder, select::placeholder { color: #93a1ab; }

    .auth-grid {
      min-height: 100vh;
      display: grid;
      grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
      background: #eef1f4;
    }

    @media (max-width: 900px) {
      .auth-grid {
        grid-template-columns: 1fr;
      }
      .brand-panel {
        padding: 32px 24px !important;
      }
      .form-panel {
        padding: 24px 20px !important;
      }
    }

    .form-input {
      width: 100%;
      padding: 11px 13px;
      border: 1px solid #e0e6eb;
      border-radius: 10px;
      background: #ffffff;
      font-family: inherit;
      font-size: 14px;
      color: #1e2a32;
      outline: none;
      transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-input:focus {
      border-color: #0f766e;
      box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
    }
    .form-input.is-invalid {
      border-color: #ef4444;
      background-color: #fef2f2;
    }

    .btn-primary-teal {
      width: 100%;
      margin-top: 6px;
      padding: 13px;
      border-radius: 11px;
      border: none;
      background: #0f766e;
      color: #ffffff;
      font-family: inherit;
      font-size: 14.5px;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: background 0.15s, transform 0.05s;
    }
    .btn-primary-teal:hover {
      background: #0b5a54;
    }
    .btn-primary-teal:active {
      transform: scale(0.99);
    }

    .tab-btn {
      padding: 9px 15px;
      border: none;
      border-radius: 9px;
      font-family: inherit;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      white-space: nowrap;
      background: transparent;
      color: #6b7a85;
      transition: all 0.15s;
    }
    .tab-btn.active {
      background: #0f766e;
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(15, 118, 110, 0.2);
    }

    .type-pill {
      padding: 9px 14px;
      border-radius: 999px;
      font-family: inherit;
      font-size: 12.5px;
      font-weight: 600;
      cursor: pointer;
      white-space: nowrap;
      border: 1px solid #e0e6eb;
      background: #ffffff;
      color: #6b7a85;
      transition: all 0.15s;
    }
    .type-pill.active {
      border-color: #0f766e;
      background: #0f766e;
      color: #ffffff;
    }

    .code-box {
      width: 100%;
      flex: 1 1 0;
      min-width: 0;
      aspect-ratio: 1;
      padding: 0;
      text-align: center;
      border: 1px solid #e0e6eb;
      border-radius: 10px;
      background: #ffffff;
      font-family: 'IBM Plex Mono', monospace;
      font-size: 18px;
      font-weight: 600;
      color: #1e2a32;
      outline: none;
      text-transform: uppercase;
      transition: border-color 0.15s, background-color 0.15s;
    }
    .code-box:focus {
      border-color: #0f766e;
      box-shadow: 0 0 0 2px rgba(15, 118, 110, 0.2);
    }
    .code-box.filled {
      border-color: #0f766e;
      background: #e2f1ef;
      color: #0f766e;
    }

    .alert-banner {
      padding: 11px 14px;
      border-radius: 9px;
      font-size: 13px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-error {
      background: #fee2e2;
      border: 1px solid #fecaca;
      color: #991b1b;
    }
    .alert-success {
      background: #dcfce7;
      border: 1px solid #bbf7d0;
      color: #166534;
    }
  </style>
</head>
<body>

@php
  $currentTab = $defaultTab ?? 'Connexion';
  if (old('action_type') === 'creer' || $errors->has('nom_clinique') || $errors->has('ville')) {
      $currentTab = 'Clinique';
  } elseif (old('action_type') === 'rejoindre' || $errors->has('code_invitation')) {
      $currentTab = 'Rejoindre';
  } elseif ($errors->has('email') && old('action_type') === null) {
      $currentTab = 'Connexion';
  }
@endphp

<div class="auth-grid">

  <!-- ======================================================== -->
  <!-- PANNEAU MARQUE GAUCHE                                    -->
  <!-- ======================================================== -->
  <div class="brand-panel" style="background: #0c2b2a; color: #cfe0dc; padding: 44px 48px; display: flex; flex-direction: column; justify-content: space-between; min-width: 0;">
    
    {{-- Header Logo Sabati --}}
    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="width: 40px; height: 40px; border-radius: 12px; background: #58c0a8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#06221f" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"></path></svg>
      </div>
      <div style="min-width: 0; line-height: 1.2;">
        <div style="font-size: 16px; font-weight: 700; color: #ffffff; letter-spacing: 0.2px;">Sabati</div>
        <div style="font-size: 11.5px; color: #7fa39d;">Gestion de clinique</div>
      </div>
    </div>

    {{-- Contenu central --}}
    <div style="max-width: 420px; margin: 36px 0;">
      <h1 style="margin: 0 0 18px; font-size: 30px; font-weight: 700; line-height: 1.25; color: #ffffff; letter-spacing: -0.01em;">
        La caisse, les dossiers et l'assurance dans un seul outil.
      </h1>
      <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #a8c6c1;">
        Suivi des prestations, encaissement multi-guichets, rapprochement de caisse et bordereaux AMO / INPS. Conçu pour les policliniques d'Afrique de l'Ouest.
      </p>
      
      <div style="display: flex; flex-direction: column; gap: 13px; margin-top: 28px;">
        <span style="display: flex; align-items: center; gap: 11px; font-size: 13.5px; color: #cfe0dc;">
          <span style="width: 24px; height: 24px; border-radius: 7px; background: #123b39; color: #58c0a8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
          </span>
          Encaissement espèces, mobile money et crédit
        </span>
        <span style="display: flex; align-items: center; gap: 11px; font-size: 13.5px; color: #cfe0dc;">
          <span style="width: 24px; height: 24px; border-radius: 7px; background: #123b39; color: #58c0a8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
          </span>
          Rapprochement et Z de caisse par guichet
        </span>
        <span style="display: flex; align-items: center; gap: 11px; font-size: 13.5px; color: #cfe0dc;">
          <span style="width: 24px; height: 24px; border-radius: 7px; background: #123b39; color: #58c0a8; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></svg>
          </span>
          Bordereaux de remboursement AMO / INPS
        </span>
      </div>
    </div>

    {{-- Footer --}}
    <div style="font-size: 12px; color: #6c8f89;">
      © 2026 Sabati · Bamako, Mali · Franc CFA
    </div>
  </div>

  <!-- ======================================================== -->
  <!-- PANNEAU FORMULAIRE DROIT                                 -->
  <!-- ======================================================== -->
  <div class="form-panel" style="display: flex; flex-direction: column; padding: 30px 40px; min-width: 0; overflow-y: auto;">

    {{-- Onglets de navigation en haut à droite --}}
    <div style="display: flex; flex-wrap: wrap; gap: 6px; align-self: flex-end; padding: 4px; border-radius: 12px; background: #ffffff; border: 1px solid #e3e8ee; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
      <button type="button" class="tab-btn {{ $currentTab === 'Connexion' ? 'active' : '' }}" onclick="switchAuthTab('Connexion')" id="btnTabConnexion">
        Connexion
      </button>
      <button type="button" class="tab-btn {{ $currentTab === 'Clinique' ? 'active' : '' }}" onclick="switchAuthTab('Clinique')" id="btnTabClinique">
        Nouvelle clinique
      </button>
      <button type="button" class="tab-btn {{ $currentTab === 'Rejoindre' ? 'active' : '' }}" onclick="switchAuthTab('Rejoindre')" id="btnTabRejoindre">
        Code d'invitation
      </button>
    </div>

    {{-- Centre du formulaire --}}
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 28px 0;">
      <div style="width: 100%; max-width: 396px;">

        {{-- Messages d'alerte ou erreurs globales --}}
        @if(session('alert'))
          <div class="alert-banner alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"></path></svg>
            <span>{{ session('alert') }}</span>
          </div>
        @endif

        @if($errors->any())
          <div class="alert-banner alert-error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span>{{ $errors->first() }}</span>
          </div>
        @endif

        {{-- ==================================================== --}}
        {{-- VUE 1 : CONNEXION                                    --}}
        {{-- ==================================================== --}}
        <div id="view_connexion" style="{{ $currentTab === 'Connexion' ? '' : 'display: none;' }}">
          <h2 style="margin: 0 0 7px; font-size: 24px; font-weight: 700; letter-spacing: -0.01em;">Connexion</h2>
          <p style="margin: 0 0 26px; font-size: 13.5px; color: #6b7a85;">Accédez à votre espace clinique.</p>

          <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 16px;">
              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Identifiant ou e-mail</span>
                <input type="text" name="email" value="{{ old('email') }}" placeholder="s.konate ou nom@clinique.ml" class="form-input @error('email') is-invalid @enderror" required autofocus />
              </label>

              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="display: flex; justify-content: space-between; align-items: baseline; font-size: 12px; font-weight: 600; color: #4a5a57;">
                  Mot de passe
                  <a href="javascript:void(0)" onclick="alert('Veuillez contacter l\'administrateur de votre clinique pour réinitialiser votre accès.')" style="font-size: 12px; font-weight: 500;">Oublié ?</a>
                </span>
                <span style="position: relative; display: flex; align-items: center;">
                  <input type="password" name="password" id="login_password" placeholder="••••••••" class="form-input" required />
                  <span onclick="togglePasswordVisibility('login_password')" style="position: absolute; right: 13px; cursor: pointer; color: #93a1ab; display: flex; align-items: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </span>
                </span>
              </label>

              <label style="display: flex; align-items: center; gap: 9px; font-size: 13px; color: #4a5a57; cursor: pointer;">
                <input type="checkbox" name="remember" value="1" style="width: 16px; height: 16px; accent-color: #0f766e; border-radius: 4px; cursor: pointer;" checked />
                Rester connecté sur ce poste
              </label>

              <button type="submit" class="btn-primary-teal">
                <span>Se connecter</span>
              </button>
            </div>
          </form>

          <div style="margin-top: 24px; padding: 14px 16px; border-radius: 11px; background: #ffffff; border: 1px solid #e3e8ee; display: flex; align-items: center; gap: 12px;">
            <span style="width: 34px; height: 34px; border-radius: 9px; background: #e2f1ef; color: #0f766e; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </span>
            <span style="font-size: 12.5px; color: #6b7a85; line-height: 1.5;">
              Vos accès dépendent de votre rôle : caisse, médecin, accueil ou administration.
            </span>
          </div>
        </div>

        {{-- ==================================================== --}}
        {{-- VUE 2 : CRÉER UNE CLINIQUE                           --}}
        {{-- ==================================================== --}}
        <div id="view_clinique" style="{{ $currentTab === 'Clinique' ? '' : 'display: none;' }}">
          <h2 style="margin: 0 0 7px; font-size: 24px; font-weight: 700; letter-spacing: -0.01em;">Créer une clinique</h2>
          <p style="margin: 0 0 22px; font-size: 13.5px; color: #6b7a85;">Vous ouvrez un nouvel établissement. Vous en serez l'administrateur.</p>

          <form method="POST" action="{{ route('register') }}" id="createClinicForm">
            @csrf
            <input type="hidden" name="action_type" value="creer">
            <input type="hidden" name="type_etablissement" id="type_etablissement_input" value="Policlinique">

            <div style="display: flex; flex-direction: column; gap: 15px;">
              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Nom de la clinique <span style="color: #ef4444;">*</span></span>
                <input type="text" name="nom_clinique" value="{{ old('nom_clinique') }}" placeholder="Policlinique Sabati" class="form-input @error('nom_clinique') is-invalid @enderror" required />
              </label>

              <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <label style="flex: 1 1 150px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Ville <span style="color: #ef4444;">*</span></span>
                  <input type="text" name="ville" value="{{ old('ville', 'Bamako') }}" placeholder="Bamako" class="form-input @error('ville') is-invalid @enderror" required />
                </label>
                <label style="flex: 1 1 130px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Téléphone</span>
                  <input type="text" name="telephone_clinique" value="{{ old('telephone_clinique') }}" placeholder="20 XX XX XX" class="form-input" />
                </label>
              </div>

              {{-- Type d'établissement pills --}}
              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Type d'établissement</span>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                  <button type="button" class="type-pill active" onclick="selectType(this, 'Policlinique')">Policlinique</button>
                  <button type="button" class="type-pill" onclick="selectType(this, 'Cabinet')">Cabinet</button>
                  <button type="button" class="type-pill" onclick="selectType(this, 'Centre de santé')">Centre de santé</button>
                </div>
              </label>

              <div style="height: 1px; background: #e3e8ee; margin: 4px 0;"></div>
              <span style="font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #93a1ab;">Compte administrateur</span>

              <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Prénom <span style="color: #ef4444;">*</span></span>
                  <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Aminata" class="form-input @error('prenom') is-invalid @enderror" required />
                </label>
                <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Nom <span style="color: #ef4444;">*</span></span>
                  <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Traoré" class="form-input @error('nom') is-invalid @enderror" required />
                </label>
              </div>

              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">E-mail <span style="color: #ef4444;">*</span></span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@clinique.ml" class="form-input @error('email') is-invalid @enderror" required />
              </label>

              <label style="display: flex; flex-direction: column; gap: 6px;">
                <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Mot de passe <span style="color: #ef4444;">*</span></span>
                <span style="position: relative; display: flex; align-items: center;">
                  <input type="password" name="password" id="create_password" placeholder="6 caractères minimum" class="form-input @error('password') is-invalid @enderror" required />
                  <span onclick="togglePasswordVisibility('create_password')" style="position: absolute; right: 13px; cursor: pointer; color: #93a1ab; display: flex; align-items: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                  </span>
                </span>
              </label>

              <button type="submit" class="btn-primary-teal">
                <span>Créer la clinique</span>
              </button>

              <p style="margin: 0; font-size: 11.5px; color: #6b7a85; line-height: 1.5; text-align: center;">
                En continuant, vous acceptez les conditions d'utilisation de Sabati.
              </p>
            </div>
          </form>
        </div>

        {{-- ==================================================== --}}
        {{-- VUE 3 : REJOINDRE AVEC CODE D'INVITATION             --}}
        {{-- ==================================================== --}}
        <div id="view_rejoindre" style="{{ $currentTab === 'Rejoindre' ? '' : 'display: none;' }}">
          <h2 style="margin: 0 0 7px; font-size: 24px; font-weight: 700; letter-spacing: -0.01em;">Rejoindre une clinique</h2>
          <p style="margin: 0 0 22px; font-size: 13.5px; color: #6b7a85;">Saisissez le code d'invitation partagé par votre clinique.</p>

          <form method="POST" action="{{ route('register') }}" id="joinClinicForm">
            @csrf
            <input type="hidden" name="action_type" value="rejoindre">
            <input type="hidden" name="code_invitation" id="code_invitation_hidden" value="{{ old('code_invitation') }}">

            {{-- 6 Cases de code interactives --}}
            <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 8px;">
              <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Code d'invitation <span style="color: #ef4444;">*</span></span>
              
              <div style="display: flex; gap: 8px;" id="codeBoxesContainer">
                <input type="text" class="code-box" maxlength="1" data-index="0" autocomplete="off" />
                <input type="text" class="code-box" maxlength="1" data-index="1" autocomplete="off" />
                <input type="text" class="code-box" maxlength="1" data-index="2" autocomplete="off" />
                <input type="text" class="code-box" maxlength="1" data-index="3" autocomplete="off" />
                <input type="text" class="code-box" maxlength="1" data-index="4" autocomplete="off" />
                <input type="text" class="code-box" maxlength="1" data-index="5" autocomplete="off" />
              </div>

              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span id="codeNote" style="font-size: 12px; color: #6b7a85;">Saisissez votre code — ex. SBT-26 ou CPAS-9988</span>
                <span id="codeDirectToggle" onclick="toggleDirectCodeInput()" style="font-size: 11.5px; color: #0f766e; cursor: pointer; text-decoration: underline;">
                  Mode texte libre
                </span>
              </div>

              {{-- Champ texte de secours pour les codes plus longs ou spéciaux --}}
              <div id="directCodeContainer" style="display: none; margin-top: 6px;">
                <input type="text" id="directCodeInput" class="form-input" style="text-transform: uppercase; font-family: 'IBM Plex Mono', monospace; font-weight: 600;" placeholder="Ex: GAH-7492" oninput="syncDirectCode(this.value)" />
              </div>
            </div>

            {{-- Badge dynamique de clinique reconnue --}}
            <div id="clinicRecognizedBadge" style="display: none; align-items: center; gap: 12px; padding: 13px 15px; border-radius: 11px; background: #e3f3ee; border: 1px solid #cfe6e2; margin-bottom: 18px;">
              <span id="badgeInitials" style="width: 38px; height: 38px; border-radius: 10px; background: #0f766e; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;">
                PS
              </span>
              <span style="min-width: 0;">
                <span id="badgeClinicName" style="display: block; font-size: 13.5px; font-weight: 700; color: #0f6b5f;">Policlinique Sabati</span>
                <span id="badgeClinicCity" style="display: block; font-size: 12px; color: #4a5a57; margin-top: 2px;">Bamako · Clinique reconnue</span>
              </span>
            </div>

            <div id="joinFormFields" style="display: block;">
              <div style="height: 1px; background: #e3e8ee; margin: 6px 0 16px;"></div>

              <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                  <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                    <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Prénom <span style="color: #ef4444;">*</span></span>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Salimata" class="form-input @error('prenom') is-invalid @enderror" required />
                  </label>
                  <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                    <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Nom <span style="color: #ef4444;">*</span></span>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Konaté" class="form-input @error('nom') is-invalid @enderror" required />
                  </label>
                </div>

                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                  <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                    <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Téléphone</span>
                    <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="76 XX XX XX" class="form-input" />
                  </label>
                  <label style="flex: 1 1 140px; display: flex; flex-direction: column; gap: 6px; min-width: 0;">
                    <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Rôle souhaité <span style="color: #ef4444;">*</span></span>
                    <select name="role_id" class="form-input" required>
                      @if(isset($roles) && count($roles) > 0)
                        @foreach($roles as $id => $nom)
                          <option value="{{ $id }}" {{ old('role_id') == $id ? 'selected' : '' }}>{{ $nom }}</option>
                        @endforeach
                      @else
                        <option value="1">Caissier</option>
                      @endif
                    </select>
                  </label>
                </div>

                <label style="display: flex; flex-direction: column; gap: 6px;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">E-mail de connexion <span style="color: #ef4444;">*</span></span>
                  <input type="email" name="email" value="{{ old('email') }}" placeholder="s.konate@clinique.ml" class="form-input @error('email') is-invalid @enderror" required />
                </label>

                <label style="display: flex; flex-direction: column; gap: 6px;">
                  <span style="font-size: 12px; font-weight: 600; color: #4a5a57;">Mot de passe <span style="color: #ef4444;">*</span></span>
                  <span style="position: relative; display: flex; align-items: center;">
                    <input type="password" name="password" id="join_password" placeholder="6 caractères minimum" class="form-input @error('password') is-invalid @enderror" required />
                    <span onclick="togglePasswordVisibility('join_password')" style="position: absolute; right: 13px; cursor: pointer; color: #93a1ab; display: flex; align-items: center;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                  </span>
                </label>

                <button type="submit" class="btn-primary-teal">
                  <span>Rejoindre la clinique</span>
                </button>
              </div>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
  // Basculement interactif entre les 3 onglets
  function switchAuthTab(tabName) {
    const tabs = ['Connexion', 'Clinique', 'Rejoindre'];
    tabs.forEach(t => {
      const btn = document.getElementById('btnTab' + t);
      const view = document.getElementById('view_' + t.toLowerCase());
      if (t === tabName) {
        if (btn) btn.classList.add('active');
        if (view) view.style.display = 'block';
      } else {
        if (btn) btn.classList.remove('active');
        if (view) view.style.display = 'none';
      }
    });
  }

  // Sélection du type d'établissement
  function selectType(btn, typeName) {
    document.querySelectorAll('.type-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const input = document.getElementById('type_etablissement_input');
    if (input) input.value = typeName;
  }

  // Affichage/Masquage mot de passe
  function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
  }

  // Gestion des cases de code d'invitation
  const codeBoxes = document.querySelectorAll('.code-box');
  const codeHidden = document.getElementById('code_invitation_hidden');
  const codeNote = document.getElementById('codeNote');
  const clinicBadge = document.getElementById('clinicRecognizedBadge');

  function updateFullCode() {
    let fullCode = '';
    codeBoxes.forEach(b => fullCode += (b.value || ''));
    if (codeHidden) {
      codeHidden.value = fullCode.toUpperCase();
    }
    checkCodeStatus(fullCode);
  }

  function checkCodeStatus(code) {
    const clean = (code || '').trim();
    if (clean.length >= 4) {
      fetch('/auth/verifier-code/' + encodeURIComponent(clean))
        .then(r => r.json())
        .then(data => {
          if (data.valide) {
            clinicBadge.style.display = 'flex';
            document.getElementById('badgeInitials').innerText = data.initiales || 'CL';
            document.getElementById('badgeClinicName').innerText = data.nom;
            document.getElementById('badgeClinicCity').innerText = data.ville + ' · Clinique reconnue';
            codeNote.innerText = 'Clinique reconnue avec succès.';
            codeNote.style.color = '#0f6b5f';
          } else {
            clinicBadge.style.display = 'none';
            codeNote.innerText = clean.length >= 6 ? 'Code non reconnu.' : 'Saisie en cours...';
            codeNote.style.color = clean.length >= 6 ? '#ef4444' : '#6b7a85';
          }
        })
        .catch(() => {
          clinicBadge.style.display = 'none';
        });
    } else {
      clinicBadge.style.display = 'none';
      codeNote.innerText = 'Saisissez votre code — ex. SBT-26 ou CPAS-9988';
      codeNote.style.color = '#6b7a85';
    }
  }

  codeBoxes.forEach((box, index) => {
    box.addEventListener('input', (e) => {
      const val = e.target.value.toUpperCase();
      box.value = val.slice(-1);
      if (box.value) {
        box.classList.add('filled');
        if (index < codeBoxes.length - 1) {
          codeBoxes[index + 1].focus();
        }
      } else {
        box.classList.remove('filled');
      }
      updateFullCode();
    });

    box.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !box.value && index > 0) {
        codeBoxes[index - 1].focus();
      }
    });

    box.addEventListener('paste', (e) => {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text').toUpperCase().replace(/[^A-Z0-9-]/g, '');
      const chars = paste.split('');
      codeBoxes.forEach((b, i) => {
        if (chars[i]) {
          b.value = chars[i];
          b.classList.add('filled');
        }
      });
      if (codeHidden) codeHidden.value = paste;
      updateFullCode();
      if (chars.length < codeBoxes.length) {
        codeBoxes[chars.length].focus();
      } else {
        codeBoxes[codeBoxes.length - 1].focus();
      }
    });
  });

  function toggleDirectCodeInput() {
    const directDiv = document.getElementById('directCodeContainer');
    const toggleBtn = document.getElementById('codeDirectToggle');
    if (directDiv.style.display === 'none') {
      directDiv.style.display = 'block';
      toggleBtn.innerText = 'Mode cases séparées';
      const directInput = document.getElementById('directCodeInput');
      if (directInput && codeHidden) {
        directInput.value = codeHidden.value;
        directInput.focus();
      }
    } else {
      directDiv.style.display = 'none';
      toggleBtn.innerText = 'Mode texte libre';
    }
  }

  function syncDirectCode(val) {
    const upper = val.toUpperCase().trim();
    if (codeHidden) codeHidden.value = upper;
    const chars = upper.split('');
    codeBoxes.forEach((b, i) => {
      b.value = chars[i] || '';
      if (b.value) b.classList.add('filled');
      else b.classList.remove('filled');
    });
    checkCodeStatus(upper);
  }

  // Initialisation à partir de la valeur existante
  document.addEventListener('DOMContentLoaded', () => {
    if (codeHidden && codeHidden.value) {
      syncDirectCode(codeHidden.value);
    }
  });
</script>

</body>
</html>
