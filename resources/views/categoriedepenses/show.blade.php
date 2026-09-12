@extends('layout')

@section('title', 'Détails de la Catégorie')

@section('content')
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Catégorie : {{ $categoriedepense->nom }}</h1>
            <div>
                <a href="{{ route('categoriedepenses.edit', $categoriedepense) }}" class="btn btn-warning">Modifier</a>
                <a href="{{ route('categoriedepenses.index') }}" class="btn btn-secondary">Retour à la liste</a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Informations Générales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><strong>Intitulé :</strong> {{ $categoriedepense->nom }}</div>
                    <div class="col-md-6 mb-3"><strong>Code Identifiant :</strong> <code>{{ $categoriedepense->code }}</code></div>
                    <div class="col-md-6 mb-3">
                        <strong>Statut :</strong>
                        @if ($categoriedepense->statut)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>
                    <div class="col-md-12 mb-3"><strong>Description :</strong><p class="text-muted mt-1">{{ $categoriedepense->description ?? 'Aucune description.' }}</p></div>
                </div>
            </div>
        </div>
    </section>
@endsection
