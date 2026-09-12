<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'CLINGEST - Gestion Clinique')</title>

    {{-- Feuilles de style Bootstrap 5 & Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Dynamic styles --}}
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #0d6efd;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-text: #94a3b8;
            --sidebar-active: #ffffff;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            margin: 0;
        }

        /* Responsive Layout Grid */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling - Figée à gauche */
        .app-sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0,0,0,0.05);
            z-index: 100;
            transition: all 0.3s ease;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid #334155;
            flex-shrink: 0;
        }

        .sidebar-menu {
            padding: 1rem 0;
            overflow-y: auto;
            flex-grow: 1;
        }

        .menu-header {
            padding: 0.75rem 1.5rem 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-active);
        }

        .nav-link-custom.active {
            background-color: var(--primary-color);
            color: #ffffff;
            font-weight: 600;
        }

        /* Main Content Container */
        .app-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Topbar Header - Figée en haut */
        .app-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.8rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .app-content {
            padding: 2rem;
            flex-grow: 1;
        }

        /* Print optimization */
        @media print {
            .app-sidebar, .app-topbar, .no-print {
                display: none !important;
            }
            .app-wrapper, .app-main, .app-content {
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
        {{-- COLONNE GAUCHE : Sidebar Menu par Modules --}}
        <aside class="app-sidebar no-print">
            {{-- En-tête de la Sidebar avec nom de la clinique --}}
            <div class="sidebar-brand">
                <i class="bi bi-hospital fs-3 text-primary"></i>
                <div>
                    <div class="lh-1">CLINGEST</div>
                    <small class="text-muted fs-7 font-weight-normal">Gahambani V1</small>
                </div>
            </div>

            {{-- Bouton d'action rapide : Création de Ticket --}}
            <div class="px-3 my-3">
                <a href="{{ route('tickets.create') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm fw-bold">
                    <i class="bi bi-plus-circle-fill"></i> Nouveau Ticket
                </a>
            </div>

            {{-- Navigation par modules --}}
            <div class="sidebar-menu">
                {{-- Dashboard --}}
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>

                {{-- MODULE 1: ACCUEIL & PATIENTS --}}
                <div class="menu-header">Accueil & Patients</div>
                <a href="{{ route('patients.index') }}" class="nav-link-custom {{ request()->routeIs('patients.*', 'patient.*') ? 'active' : '' }}">
                    <i class="bi bi-person-vcard"></i> Gestion Patients
                </a>
                <a href="{{ route('cartesassurances.index') }}" class="nav-link-custom {{ request()->routeIs('cartesassurances.*', 'carteassurance.*') ? 'active' : '' }}">
                    <i class="bi bi-card-heading"></i> Cartes d'Assurance
                </a>

                {{-- MODULE 2: CAISSE & FACTURATION --}}
                <div class="menu-header">Caisse & Facturation</div>
                <a href="{{ route('tickets.index') }}" class="nav-link-custom {{ request()->routeIs('tickets.*', 'ticket.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt"></i> Tickets / Factures
                </a>
                <a href="{{ route('paiements.index') }}" class="nav-link-custom {{ request()->routeIs('paiements.*', 'paiement.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front"></i> <strong>Gestion des Paiements</strong>
                </a>
                <a href="{{ route('modepaiements.index') }}" class="nav-link-custom {{ request()->routeIs('modepaiements.*', 'modepaiement.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i> Modes de Paiement
                </a>
                <a href="{{ route('dettes.index') }}" class="nav-link-custom {{ request()->routeIs('dettes.*', 'dette.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i> Dettes & Impayés
                </a>
                <a href="{{ route('caisses.index') }}" class="nav-link-custom {{ request()->routeIs('caisses.*', 'caisse.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i> Session de Caisse
                </a>
                <a href="{{ route('mouvementcaisses.index') }}" class="nav-link-custom {{ request()->routeIs('mouvementcaisses.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i> Mouvements Espèces
                </a>

                {{-- MODULE 3: PRESTATIONS & MÉDECINS --}}
                <div class="menu-header">Services & Prestations</div>
                <a href="{{ route('services.index') }}" class="nav-link-custom {{ request()->routeIs('services.*', 'service.*') ? 'active' : '' }}">
                    <i class="bi bi-building"></i> Services Médicaux
                </a>
                <a href="{{ route('prestations.index') }}" class="nav-link-custom {{ request()->routeIs('prestations.*', 'prestation.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-medical"></i> Actes & Prestations
                </a>
                <a href="{{ route('tarifs.index') }}" class="nav-link-custom {{ request()->routeIs('tarifs.*', 'tarif.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Grille Tarifaire
                </a>
                <a href="{{ route('medecins.index') }}" class="nav-link-custom {{ request()->routeIs('medecins.*', 'medecin.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Médecins
                </a>
                <a href="{{ route('remunerations.index') }}" class="nav-link-custom {{ request()->routeIs('remunerations.*', 'reglespartage.*') ? 'active' : '' }}">
                    <i class="bi bi-calculator"></i> Rémunérations & Honoraires
                </a>

                {{-- MODULE 4: COMPTABILITÉ & CHARGES --}}
                <div class="menu-header">Comptabilité & Charges</div>
                <a href="{{ route('recettes.index') }}" class="nav-link-custom {{ request()->routeIs('recettes.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i> Recettes
                </a>
                <a href="{{ route('depenses.index') }}" class="nav-link-custom {{ request()->routeIs('depenses.*', 'categoriedepenses.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-down-arrow"></i> Dépenses & Charges
                </a>

                {{-- MODULE 5: ASSURANCES & TIERS PAYANT --}}
                <div class="menu-header">Assurances & Tiers Payant</div>
                <a href="{{ route('assurances.index') }}" class="nav-link-custom {{ request()->routeIs('assurances.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Organismes d'Assurance
                </a>
                <a href="{{ route('rapports.assurances') }}" class="nav-link-custom {{ request()->routeIs('rapports.assurances') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Relevé Tiers Payant
                </a>

                {{-- MODULE 6: RAPPORTS & AUDIT --}}
                <div class="menu-header">Rapports & Audit</div>
                <a href="{{ route('rapports.index') }}" class="nav-link-custom {{ request()->routeIs('rapports.index', 'rapports.medecins') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> Centre de Rapports
                </a>
                <a href="{{ route('journalactivites.index') }}" class="nav-link-custom {{ request()->routeIs('journalactivites.*') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i> Journal d'Activité
                </a>

                {{-- MODULE 7: ADMINISTRATION --}}
                <div class="menu-header">Administration</div>
                <a href="{{ route('users.index') }}" class="nav-link-custom {{ request()->routeIs('users.*', 'user.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
                <a href="{{ route('roles.index') }}" class="nav-link-custom {{ request()->routeIs('roles.*', 'role.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Rôles & Permissions
                </a>
                <a href="{{ route('employes.index') }}" class="nav-link-custom {{ request()->routeIs('employes.*') ? 'active' : '' }}">
                    <i class="bi bi-person-lines-fill"></i> Employés
                </a>
            </div>
        </aside>

        {{-- COLONNE DROITE (ÉLARGIE) : Zone Principale de l'application --}}
        <div class="app-main">
            {{-- Topbar supérieure avec statut et profil --}}
            <header class="app-topbar no-print">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                        <i class="bi bi-circle-fill me-1 small"></i> Caisse Ouverte
                    </span>
                    <span class="text-muted small">
                        <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                        <i class="bi bi-plus-lg"></i> Nouveau Ticket
                    </a>
                    <a href="{{ route('paiements.create') }}" class="btn btn-sm btn-outline-success fw-semibold">
                        <i class="bi bi-credit-card-2-front"></i> Nouveau Paiement
                    </a>
                    <a href="{{ route('patient.create') }}" class="btn btn-sm btn-outline-secondary fw-semibold">
                        <i class="bi bi-person-plus"></i> Nouveau Patient
                    </a>
                    <div class="vr mx-1"></div>

                    @guest
                        {{-- Boutons pour utilisateurs non-connectés --}}
                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary fw-bold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Se Connecter
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                            <i class="bi bi-person-plus me-1"></i> S'inscrire
                        </a>
                    @else
                        {{-- Profil et bouton de déconnexion pour l'utilisateur connecté --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                {{ strtoupper(substr(auth()->user()->nom ?? auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="lh-1 me-2">
                                <div class="fw-bold small">{{ auth()->user()->name ?? 'Utilisateur' }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->role->nom ?? 'Agent / Caissier' }}</small>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Se déconnecter">
                                    <i class="bi bi-power"></i> <span class="d-none d-md-inline">Se déconnecter</span>
                                </button>
                            </form>
                        </div>
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

    {{-- Inclusion des scripts JavaScript d'autocomplétion --}}
    <script src="{{ asset('js/patient-autocomplete.js') }}"></script>
    <script src="{{ asset('js/service-autocomplete.js') }}"></script>

    {{-- Emplacement d'injection pour d'éventuels scripts additionnels --}}
    @stack('scripts')
  </body>
</html>
