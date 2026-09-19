<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abonnement Suspendu - CLINGEST SaaS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .suspension-card {
            max-width: 520px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        .icon-wrapper {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: #fef2f2;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="suspension-card">
        <div class="icon-wrapper">
            <i data-lucide="shield-alert" style="width: 34px; height: 34px;"></i>
        </div>

        <h3 class="fw-bold mb-2">Accès Temporairement Suspendu</h3>
        <p class="text-muted mb-4">
            L'abonnement de votre établissement médical <strong>{{ auth()->user()->clinique->nom ?? 'votre clinique' }}</strong> est actuellement inactif ou arrivé à échéance.
        </p>

        <div class="p-3 bg-light rounded-3 text-start mb-4 border small">
            <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-semibold">
                <i data-lucide="info" style="width: 16px; height: 16px;" class="text-primary"></i>
                <span>Que devez-vous faire ?</span>
            </div>
            <p class="mb-1 text-muted">
                Pour réactiver immédiatement vos accès ou régulariser votre licence d'utilisation, veuillez contacter l'administrateur de la plateforme CLINGEST Santé.
            </p>
            <div class="mt-2 text-muted">
                <strong>Support Plateforme :</strong> support@clingest.com / (+223) 76 00 00 00
            </div>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-2 px-4 py-2 fw-semibold">
                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                    <span>Se déconnecter</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
