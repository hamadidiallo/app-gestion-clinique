<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', (auth()->check() && auth()->user()->clinique ? auth()->user()->clinique->nom . ' - CLINGEST' : 'CLINGEST Santé'))</title>

    {{-- Google Fonts : IBM Plex Sans & IBM Plex Mono (de doc/Clinique.dc.html) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Feuilles de style Bootstrap 5 & Fallback Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Bibliothèque Officielle Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Design System Médical Moderne (Inspiré de doc/Clinique.dc.html) --}}
    <style>
        :root {
            --sidebar-width: 254px;
            --primary-color: #0f766e;
            --primary-hover: #0b5a54;
            --primary-soft: #e2f1ef;
            --primary-tint: #e3f3ee;
            --sidebar-bg: #ffffff;
            --sidebar-border: #e3e8ee;
            --sidebar-text: #6b7a85;
            --sidebar-text-active: #0f766e;
            --app-bg: #eef1f4;
            --card-border: #e6ebf0;
            --text-main: #1e2a32;
            --text-muted: #6b7a85;
        }

        *, *::before, *::after {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'IBM Plex Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--app-bg);
            color: var(--text-main);
            -webkit-font-smoothing: antialiased;
        }

        /* Classes utilitaires pour police monospace */
        .font-mono, .mono-val {
            font-family: 'IBM Plex Mono', monospace !important;
        }

        /* Rendu optimal des icônes Lucide */
        .lucide {
            width: 1.15rem;
            height: 1.15rem;
            stroke-width: 1.9;
            vertical-align: -0.15em;
            display: inline-block;
            flex-shrink: 0;
        }
        .lucide-sm { width: 0.95rem; height: 0.95rem; }
        .lucide-lg { width: 1.35rem; height: 1.35rem; }

        /* Responsive Layout Grid - Full App Shell */
        .app-wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
            overflow: hidden;
            background-color: var(--app-bg);
        }

        /* Sidebar Styling (Blanc médical, propre et épuré) */
        .app-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            max-height: 100vh;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            border-right: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 1.1rem 1.15rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--sidebar-border);
            flex-shrink: 0;
            background-color: #ffffff;
        }

        .sidebar-brand .brand-icon {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background-color: var(--primary-color);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(15, 118, 110, 0.25);
        }

        .sidebar-brand .brand-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.15;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .sidebar-menu {
            padding: 0.5rem 0.65rem 2.25rem;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1 1 0%;
            min-height: 0;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar-menu::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .menu-header {
            padding: 0.65rem 0.65rem 0.2rem;
            font-size: 0.66rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
            font-weight: 700;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.42rem 0.7rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.825rem;
            font-weight: 500;
            border-radius: 8px;
            margin-bottom: 2px;
            transition: all 0.15s ease;
        }

        .nav-link-custom [data-lucide],
        .nav-link-custom svg {
            width: 17px;
            height: 17px;
            stroke-width: 1.9;
            flex-shrink: 0;
        }

        .nav-link-custom:hover {
            background-color: #f1f5f9;
            color: var(--text-main);
        }

        .nav-link-custom.active {
            background-color: var(--primary-soft);
            color: var(--primary-color);
            font-weight: 600;
        }

        .sidebar-user-footer {
            padding: 0.75rem 0.85rem;
            border-top: 1px solid var(--sidebar-border);
            background-color: #ffffff;
            flex-shrink: 0;
        }

        .sidebar-user-card {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.5rem 0.65rem;
            border-radius: 8px;
            background-color: var(--primary-soft);
        }

        .sidebar-user-card .avatar {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            background-color: var(--primary-color);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
            flex-shrink: 0;
        }

        /* Main Content Container */
        .app-main {
            flex: 1 1 0%;
            min-width: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* Topbar Header */
        .app-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.75rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
            z-index: 90;
        }

        .app-content {
            flex: 1 1 0%;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1.5rem 1.75rem;
        }

        /* Cards & Components Styling */
        .card {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            background-color: #ffffff;
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--card-border);
            font-weight: 600;
            padding: 0.95rem 1.25rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .btn {
            border-radius: 9px;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.45rem 0.95rem;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover, .btn-outline-primary:focus, .btn-outline-primary.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .table {
            --bs-table-bg: transparent;
        }

        .table thead th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--card-border);
            background-color: #f8fafc;
            padding: 0.7rem 1rem;
        }

        .table tbody td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #f1f4f6;
            vertical-align: middle;
            font-size: 0.875rem;
        }

        /* Badges Pilules (inspirées de doc/Clinique.dc.html) */
        .badge {
            font-weight: 600;
            letter-spacing: 0.02em;
            padding: 0.35em 0.75em;
            border-radius: 999px;
            font-size: 0.75rem;
        }

        .badge-success-pill {
            background-color: #e3f3ee;
            color: #0f6b5f;
            border: 1px solid #c2e5dc;
        }

        .badge-warning-pill {
            background-color: #fdf0d5;
            color: #8a5712;
            border: 1px solid #f2ddb3;
        }

        .badge-danger-pill {
            background-color: #fce4e4;
            color: #b3261e;
            border: 1px solid #f0c9c2;
        }

        .badge-info-pill {
            background-color: #e2f1ef;
            color: #0f766e;
            border: 1px solid #cfe6e2;
        }

        /* Print optimization */
        @media print {
            .app-sidebar, .app-topbar, .no-print {
                display: none !important;
            }
            html, body, .app-wrapper, .app-main, .app-content {
                height: auto !important;
                overflow: visible !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                background: white !important;
            }
        }
    </style>

    @vite(['resources/css/app.css'])
  </head>
  <body>
    <div class="app-wrapper">
        {{-- COLONNE GAUCHE : Sidebar Menu avec Lucide Icons --}}
        <aside class="app-sidebar no-print">
            {{-- En-tête de la Sidebar --}}
            <div class="sidebar-brand">
                @if(auth()->check() && auth()->user()->clinique && auth()->user()->clinique->logo)
                    <div class="brand-icon p-1 bg-white border" style="width: 36px; height: 36px; border-radius: 9px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img src="{{ asset('storage/' . auth()->user()->clinique->logo) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                @else
                    <div class="brand-icon">
                        <i data-lucide="cross"></i>
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="brand-title">{{ auth()->check() && auth()->user()->clinique ? auth()->user()->clinique->nom : 'CLINGEST' }}</div>
                    <div class="brand-sub">{{ auth()->check() && auth()->user()->clinique ? auth()->user()->clinique->ville . ' · ' . auth()->user()->clinique->pays : 'Plateforme Médicale SaaS' }}</div>
                </div>
            </div>

            {{-- Bouton d'action rapide : Nouveau Ticket (Réception, Caisse, Admin) --}}
            @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Réceptionniste', 'Caissier']))
            <div class="px-3 pt-2 pb-1 flex-shrink-0">
                <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm fw-bold" style="font-size: 0.82rem; border-radius: 8px;">
                    <i data-lucide="plus-circle" class="lucide-sm"></i>
                    <span>Nouveau Ticket</span>
                </a>
            </div>
            @endif

            {{-- Navigation par modules avec Lucide Icons et Filtrage RBAC --}}
            <div class="sidebar-menu">
                {{-- Dashboard (Visible pour tous) --}}
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Tableau de bord</span>
                </a>

                {{-- MODULE 1: ACCUEIL & DOSSIER MÉDICAL --}}
                <div class="menu-header">Accueil & Médical</div>
                <a href="{{ route('patients.index') }}" class="nav-link-custom {{ request()->routeIs('patients.*', 'patient.*') ? 'active' : '' }}">
                    <i data-lucide="users"></i>
                    <span>Gestion Patients</span>
                </a>
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Médecin', 'Réceptionniste']))
                <a href="{{ route('consultations.index') }}" class="nav-link-custom {{ request()->routeIs('consultations.*') ? 'active' : '' }}">
                    <i data-lucide="activity"></i>
                    <span>Consultations & Constantes</span>
                </a>
                @endif

                {{-- MODULE 2: CAISSE & FACTURATION (Caissier, Comptable, Réceptionniste, Admin) --}}
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Caissier', 'Comptable', 'Réceptionniste']))
                <div class="menu-header">Caisse & Facturation</div>
                <a href="{{ route('tickets.index') }}" class="nav-link-custom {{ request()->routeIs('tickets.*', 'ticket.*') ? 'active' : '' }}">
                    <i data-lucide="receipt"></i>
                    <span>Tickets / Factures</span>
                </a>
                @if(auth()->user()->hasRole(['Administrateur', 'Caissier', 'Comptable']))
                <a href="{{ route('paiements.index') }}" class="nav-link-custom {{ request()->routeIs('paiements.*', 'paiement.*') ? 'active' : '' }}">
                    <i data-lucide="credit-card"></i>
                    <span>Journal des Encaissements</span>
                </a>
                <a href="{{ route('dettes.index') }}" class="nav-link-custom {{ request()->routeIs('dettes.*', 'dette.*') ? 'active' : '' }}">
                    <i data-lucide="alert-triangle"></i>
                    <span>Dettes & Impayés</span>
                </a>
                <a href="{{ route('caisses.index') }}" class="nav-link-custom {{ request()->routeIs('caisses.*', 'caisse.*', 'mouvementcaisses.*') ? 'active' : '' }}">
                    <i data-lucide="banknote"></i>
                    <span>Caisse & Clôture Journalière</span>
                </a>
                <a href="{{ route('modepaiements.index') }}" class="nav-link-custom {{ request()->routeIs('modepaiements.*', 'modepaiement.*') ? 'active' : '' }}">
                    <i data-lucide="wallet"></i>
                    <span>Modes de Paiement</span>
                </a>
                @endif
                @endif

                {{-- MODULE 3: SERVICES & SOINS MÉDICAUX --}}
                <div class="menu-header">Soins & Corps Médical</div>
                <a href="{{ route('actes.index') }}" class="nav-link-custom {{ request()->routeIs('actes.*', 'acte.*') ? 'active' : '' }}">
                    <i data-lucide="book-open"></i>
                    <span>Catalogue des Actes</span>
                </a>
                <a href="{{ route('prestations.index') }}" class="nav-link-custom {{ request()->routeIs('prestations.*', 'prestation.*') ? 'active' : '' }}">
                    <i data-lucide="activity"></i>
                    <span>Soins aux Patients</span>
                </a>
                <a href="{{ route('services.index') }}" class="nav-link-custom {{ request()->routeIs('services.*', 'service.*') ? 'active' : '' }}">
                    <i data-lucide="building-2"></i>
                    <span>Services Médicaux</span>
                </a>
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Médecin', 'Comptable']))
                <a href="{{ route('medecins.index') }}" class="nav-link-custom {{ request()->routeIs('medecins.*', 'medecin.*') ? 'active' : '' }}">
                    <i data-lucide="user-check"></i>
                    <span>Médecins Praticiens</span>
                </a>
                <a href="{{ route('remunerations.index') }}" class="nav-link-custom {{ request()->routeIs('remunerations.*', 'reglespartage.*') ? 'active' : '' }}">
                    <i data-lucide="calculator"></i>
                    <span>Rémunérations & Paies</span>
                </a>
                @endif

                {{-- MODULE 4: COMPTABILITÉ & FINANCES (Comptable & Admin uniquement) --}}
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Comptable']))
                <div class="menu-header">Comptabilité</div>
                <a href="{{ route('recettes.index') }}" class="nav-link-custom {{ request()->routeIs('recettes.*') ? 'active' : '' }}">
                    <i data-lucide="trending-up"></i>
                    <span>Recettes</span>
                </a>
                <a href="{{ route('depenses.index') }}" class="nav-link-custom {{ request()->routeIs('depenses.*', 'categoriedepenses.*') ? 'active' : '' }}">
                    <i data-lucide="trending-down"></i>
                    <span>Dépenses & Charges</span>
                </a>
                @endif

                {{-- MODULE 5: ASSURANCES & TIERS PAYANT --}}
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Comptable', 'Caissier', 'Réceptionniste']))
                <div class="menu-header">Assurances</div>
                <a href="{{ route('assurances.index') }}" class="nav-link-custom {{ request()->routeIs('assurances.*') ? 'active' : '' }}">
                    <i data-lucide="shield-check"></i>
                    <span>Organismes Assurance</span>
                </a>
                @if(auth()->user()->hasRole(['Administrateur', 'Comptable']))
                <a href="{{ route('rapports.assurances') }}" class="nav-link-custom {{ request()->routeIs('rapports.assurances') ? 'active' : '' }}">
                    <i data-lucide="file-spreadsheet"></i>
                    <span>Relevé Tiers Payant</span>
                </a>
                @endif
                @endif

                {{-- MODULE 6: RAPPORTS & AUDIT --}}
                @if(auth()->check() && auth()->user()->hasRole(['Administrateur', 'Comptable', 'Médecin']))
                <div class="menu-header">Rapports & Audit</div>
                <a href="{{ route('rapports.index') }}" class="nav-link-custom {{ request()->routeIs('rapports.index', 'rapports.medecins') ? 'active' : '' }}">
                    <i data-lucide="bar-chart-3"></i>
                    <span>Centre de Rapports</span>
                </a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('journalactivites.index') }}" class="nav-link-custom {{ request()->routeIs('journalactivites.*') ? 'active' : '' }}">
                    <i data-lucide="history"></i>
                    <span>Journal d'Activité</span>
                </a>
                @endif
                @endif

                {{-- MODULE 7: ADMINISTRATION (Administrateur uniquement) --}}
                @if(auth()->check() && auth()->user()->isAdmin())
                <div class="menu-header">Administration</div>
                <a href="{{ route('users.index') }}" class="nav-link-custom {{ request()->routeIs('users.*', 'user.*') ? 'active' : '' }}">
                    <i data-lucide="user-cog"></i>
                    <span>Utilisateurs</span>
                </a>
                <a href="{{ route('roles.index') }}" class="nav-link-custom {{ request()->routeIs('roles.*', 'role.*') ? 'active' : '' }}">
                    <i data-lucide="key-round"></i>
                    <span>Rôles & Accès</span>
                </a>
                <a href="{{ route('abonnement.index') }}" class="nav-link-custom {{ request()->routeIs('abonnement.*') ? 'active' : '' }}">
                    <i data-lucide="sparkles"></i>
                    <span>Abonnement & Licence</span>
                </a>
                <a href="{{ route('parametres.index') }}" class="nav-link-custom {{ request()->routeIs('parametres.*') ? 'active' : '' }}">
                    <i data-lucide="settings"></i>
                    <span>Paramètres Clinique</span>
                </a>
                @endif
            </div>


            {{-- Profil Utilisateur en bas de Sidebar (Style doc/Clinique.dc.html) --}}
            @auth
            <div class="sidebar-user-footer">
                <div class="sidebar-user-card">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->nom ?? auth()->user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="min-w-0" style="flex: 1;">
                        <div class="fw-bold text-truncate" style="font-size: 0.82rem;">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                        <div class="text-muted text-truncate" style="font-size: 0.7rem;">
                            {{ auth()->user()->role->nom ?? 'Personnel' }}
                            @if(auth()->user()->clinique)
                                · <strong class="text-teal font-mono">{{ auth()->user()->clinique->code }}</strong>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endauth
        </aside>

        {{-- COLONNE DROITE : Zone Principale de l'application --}}
        <div class="app-main">
            {{-- Topbar supérieure épurée --}}
            <header class="app-topbar no-print">
                @php
                    $topbarCaisse = \App\Models\Caisse::where('statut', 'ouverte')->first();
                @endphp
                <div class="d-flex align-items-center gap-3">
                    @if($topbarCaisse)
                        <a href="{{ route('caisses.show', $topbarCaisse) }}" class="text-decoration-none">
                            <span class="badge badge-success-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: #12a594; display: inline-block;"></span>
                                <span>Caisse Ouverte : <strong class="font-mono fw-bold">{{ number_format($topbarCaisse->solde_actuel ?? 0, 0, ',', ' ') }} FCFA</strong></span>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('caisses.index') }}" class="text-decoration-none">
                            <span class="badge badge-warning-pill px-3 py-2 d-inline-flex align-items-center gap-2">
                                <i data-lucide="lock" class="lucide-sm"></i>
                                <span>Caisse Fermée</span>
                            </span>
                        </a>
                    @endif
                    <span class="d-none d-lg-inline-flex align-items-center gap-2 text-muted small">
                        <span class="fw-semibold text-primary" style="color: var(--primary-color) !important;">Journée en cours</span>
                        <span>·</span>
                        <span class="font-mono">{{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</span>
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i data-lucide="plus" class="lucide-sm"></i>
                        <span>Nouveau Ticket</span>
                    </a>
                    <a href="{{ route('patient.create') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                        <i data-lucide="user-plus" class="lucide-sm"></i>
                        <span>Nouveau Patient</span>
                    </a>
                    <div class="vr mx-1"></div>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary fw-bold d-inline-flex align-items-center gap-1">
                            <i data-lucide="log-in" class="lucide-sm"></i>
                            <span>Connexion</span>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                            <i data-lucide="user-plus" class="lucide-sm"></i>
                            <span>S'inscrire</span>
                        </a>
                    @else
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" title="Se déconnecter">
                                <i data-lucide="power" class="lucide-sm"></i>
                                <span class="d-none d-md-inline">Déconnexion</span>
                            </button>
                        </form>
                    @endguest
                </div>
            </header>

            {{-- Zone de Contenu Principale --}}
            <main class="app-content">
                {{-- Composants d'affichage des messages d'erreur et d'alerte --}}
                <x-form.erreur />
                <x-form.alert />

                {{-- Contenu de la vue spécifique injecté dynamiquement --}}
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Script Bootstrap Bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Bibliothèques d'exportation Excel & PDF --}}
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="{{ asset('js/table-export.js') }}"></script>

    {{-- Scripts JavaScript d'autocomplétion --}}
    <script src="{{ asset('js/patient-autocomplete.js') }}"></script>
    <script src="{{ asset('js/service-autocomplete.js') }}"></script>

    {{-- Initialisation Globale des Icônes Lucide et Maintien de l'élément Actif du Menu en vue --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
            var activeNav = document.querySelector('.sidebar-menu .nav-link-custom.active');
            if (activeNav) {
                activeNav.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }
        });
    </script>

    {{-- Emplacement d'injection pour d'éventuels scripts additionnels --}}
    @stack('scripts')
  </body>
</html>
