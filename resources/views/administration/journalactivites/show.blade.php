@extends('layout')

@section('title', 'Détails de l\'Activité')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Log d'Audit #{{ $journalactivite->id }}</h1>
            <a href="{{ route('journalactivites.index') }}" class="btn btn-secondary">Retour à la liste</a>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">Rapport d'Événement Système</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Utilisateur Auteur :</strong> {{ $journalactivite->user->name ?? 'Système' }}</div>
                    <div class="col-md-6 mb-3"><strong>Date & Heure :</strong> {{ $journalactivite->date_action ? $journalactivite->date_action->format('d/m/Y H:i:s') : '-' }}</div>
                    <div class="col-md-4 mb-3"><strong>Action Exécutée :</strong> <span class="badge bg-primary fs-6">{{ $journalactivite->action }}</span></div>
                    <div class="col-md-4 mb-3"><strong>Module Système :</strong> <span class="badge bg-secondary fs-6">{{ $journalactivite->module }}</span></div>
                    <div class="col-md-4 mb-3"><strong>Adresse IP :</strong> <code>{{ $journalactivite->adresse_ip ?? '127.0.0.1' }}</code></div>
                    <div class="col-md-6 mb-3"><strong>Entité Ciblée :</strong> {{ $journalactivite->objet_type }}</div>
                    <div class="col-md-6 mb-3"><strong>Identifiant Cible :</strong> #{{ $journalactivite->objet_id }}</div>
                    <div class="col-md-12 mb-3"><strong>Description de l'action :</strong><p class="text-muted mt-1">{{ $journalactivite->description ?? 'Aucune description fournie.' }}</p></div>

                    @if(!empty($journalactivite->anciennes_valeurs))
                        <div class="col-md-6 mb-3">
                            <strong>Anciennes Valeurs :</strong>
                            <pre class="bg-light p-3 border rounded mt-1"><code>{{ json_encode($journalactivite->anciennes_valeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    @endif

                    @if(!empty($journalactivite->nouvelles_valeurs))
                        <div class="col-md-6 mb-3">
                            <strong>Nouvelles Valeurs :</strong>
                            <pre class="bg-light p-3 border rounded mt-1"><code>{{ json_encode($journalactivite->nouvelles_valeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
