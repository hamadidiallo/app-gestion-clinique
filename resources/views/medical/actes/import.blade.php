@extends('layout')

@section('title', 'Importer des examens de laboratoire')

@section('content')
<div class="container-fluid p-0">
    {{-- En-tête --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('actes.index') }}" class="btn btn-light border bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" aria-label="Retour au catalogue des actes">
            <i data-lucide="arrow-left" style="width: 1.15rem; height: 1.15rem;"></i>
        </a>
        <div>
            <h1 class="h3 fw-bold mb-0">Importer des examens de laboratoire</h1>
            <div class="text-muted">Ajoutez les examens et leurs paramètres sans les saisir un par un.</div>
        </div>
    </div>

    @if (session('bilan'))
        @php($bilan = session('bilan'))
        <div class="alert alert-success border-0 shadow-sm" role="status">
            <div class="fw-bold mb-1">Import terminé</div>
            {{ $bilan['examens_crees'] }} examen(s) créé(s), {{ $bilan['examens_completes'] }} complété(s) ·
            {{ $bilan['parametres_ajoutes'] }} paramètre(s) ajouté(s){{ $bilan['parametres_mis_a_jour'] ? ', '.$bilan['parametres_mis_a_jour'].' mis à jour' : '' }}.
            @if ($bilan['examens_crees'])
                <div class="mt-1">Les nouveaux examens sont <strong>inactifs tant que leur tarif n'est pas renseigné</strong> : ouvrez chacun d'eux dans le catalogue des actes pour saisir son prix.</div>
            @endif
        </div>
    @endif

    @if (session('erreursImport'))
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <div class="fw-bold mb-1">Le fichier n'a pas été importé</div>
            <div class="small mb-2">Corrigez ces lignes puis relancez l'import. Rien n'a été modifié.</div>
            <ul class="mb-0 small">
                @foreach (session('erreursImport') as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        {{-- Catalogue de départ --}}
        <div class="col-xl-7">
            <form method="POST" action="{{ route('actes.import.catalogue') }}" class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                @csrf
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h2 class="h5 fw-bold mb-1">Catalogue de départ MediGest</h2>
                    <p class="small text-muted mb-0">
                        {{ $catalogue->count() }} examens courants, avec leurs paramètres et des valeurs normales usuelles (adulte, homme, femme, enfant).
                        <strong>Ces valeurs sont à vérifier par votre laboratoire</strong> : elles dépendent de la méthode et de l'automate utilisés.
                    </p>
                </div>
                <div class="card-body px-4">
                    @error('codes')<div class="alert alert-warning py-2 small">{{ $message }}</div>@enderror

                    <label class="d-flex align-items-center gap-2 small fw-semibold mb-2">
                        <input type="checkbox" class="form-check-input m-0" onchange="document.querySelectorAll('input[name=&quot;codes[]&quot;]').forEach(c => c.checked = this.checked)" checked>
                        Tout cocher
                    </label>

                    <div class="border rounded-3" style="max-height: 460px; overflow-y: auto;">
                        @foreach ($catalogue as $examen)
                            @php($dejaPresent = in_array($examen['code'], $codesExistants, true))
                            <label class="d-flex align-items-start gap-3 px-3 py-2 border-bottom" style="cursor: pointer;">
                                <input type="checkbox" name="codes[]" value="{{ $examen['code'] }}" class="form-check-input mt-1" checked>
                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">{{ $examen['nom'] }}</span>
                                    <span class="d-block small text-muted">
                                        {{ collect($examen['parametres'])->pluck('nom')->implode(', ') }}
                                    </span>
                                </span>
                                @if ($dejaPresent)
                                    <span class="badge rounded-pill text-nowrap" style="background: #e2f1ef; color: #0f766e;" title="L'examen existe déjà : seuls ses paramètres manquants seront ajoutés">Déjà présent</span>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    <p class="small text-muted mt-3 mb-0">
                        Un examen déjà présent dans votre clinique n'est pas dupliqué : seuls ses paramètres manquants sont ajoutés, et vos valeurs déjà ajustées sont conservées.
                    </p>
                </div>
                <div class="card-footer bg-white border-0 px-4 pb-4 pt-0">
                    <button type="submit" class="btn btn-teal fw-semibold w-100">Importer les examens cochés</button>
                </div>
            </form>
        </div>

        {{-- Fichier Excel / CSV --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h2 class="h5 fw-bold mb-1">Votre propre liste (Excel)</h2>
                    <p class="small text-muted mb-0">Pour importer la liste de votre laboratoire ou d'une nomenclature (ex. CANAM).</p>
                </div>
                <div class="card-body px-4">
                    <ol class="small ps-3 mb-4">
                        <li class="mb-2">
                            Téléchargez le modèle et ouvrez-le dans Excel.
                            <div class="mt-2">
                                <a href="{{ route('actes.import.modele') }}" class="btn btn-sm btn-outline-teal d-inline-flex align-items-center gap-1">
                                    <i data-lucide="download" class="lucide-sm"></i> Télécharger le modèle
                                </a>
                            </div>
                        </li>
                        <li class="mb-2">Une ligne par paramètre. Répétez le <strong>code de l'examen</strong> sur chaque ligne ; le nom et le tarif ne sont nécessaires qu'une fois.</li>
                        <li class="mb-2">Colonnes <code>min</code>/<code>max</code> pour les valeurs générales, et <code>min_homme</code>, <code>min_femme</code>, <code>min_enfant</code>… si elles diffèrent. Pour un résultat en texte, remplissez <code>valeur_normale_texte</code> (ex. Négatif).</li>
                        <li>Dans Excel : <strong>Fichier › Enregistrer sous › CSV (séparateur : point-virgule)</strong>, puis importez le fichier ci-dessous.</li>
                    </ol>

                    <form method="POST" action="{{ route('actes.import.fichier') }}" enctype="multipart/form-data">
                        @csrf
                        <label for="fichier" class="form-label small fw-semibold">Fichier CSV</label>
                        <input type="file" name="fichier" id="fichier" accept=".csv,text/csv" class="form-control @error('fichier') is-invalid @enderror" required>
                        @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <p class="small text-muted mt-2">Les valeurs du fichier remplacent celles déjà enregistrées pour les mêmes examens. Si une ligne contient une erreur, rien n'est importé.</p>
                        <button type="submit" class="btn btn-teal fw-semibold w-100">Importer le fichier</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
