{{-- Paramètres mesurés par un examen de laboratoire, avec leurs valeurs normales --}}
@php
    $parametres = $acte->parametres;
    $peutModifier = auth()->user()?->hasRole('Administrateur');
@endphp
<div class="card border-0 shadow-sm mt-4" style="border-radius: 14px;" id="parametres">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <h2 class="h5 fw-bold mb-1">Paramètres de l'examen</h2>
        <p class="small text-muted mb-0">Ce que le laboratoire mesure pour cet examen. Une valeur saisie hors des valeurs normales ressortira en rouge sur le compte-rendu.</p>
    </div>
    <div class="card-body px-4 pb-4">
        @if ($parametres->isEmpty())
            <div class="p-3 rounded-3 small text-muted mt-2" style="border: 1px dashed #cfdcd7;">
                Aucun paramètre pour l'instant. Sans paramètre, le laboratoire ne pourra saisir qu'une conclusion libre.
            </div>
        @else
            <div class="table-responsive mt-2">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr class="small text-muted text-uppercase" style="letter-spacing: .05em;">
                            <th>Paramètre</th>
                            <th>Valeurs normales</th>
                            @if ($peutModifier)<th class="text-end">Actions</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($parametres as $parametre)
                            <tr>
                                <td class="fw-semibold">{{ $parametre->nom }}</td>
                                <td class="font-mono">{{ $parametre->valeurs_normales ?: '—' }}</td>
                                @if ($peutModifier)
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('actes.parametres.destroy', [$acte, $parametre]) }}" class="d-inline" onsubmit="return confirm('Supprimer le paramètre « {{ e($parametre->nom) }} » ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" aria-label="Supprimer le paramètre {{ $parametre->nom }}">
                                                <i data-lucide="trash-2" class="lucide-sm"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($peutModifier)
            <form method="POST" action="{{ route('actes.parametres.store', $acte) }}" class="mt-4 p-3 rounded-3" style="background: #f6f9f8;" id="form-parametre">
                @csrf
                <div class="fw-semibold mb-3">Ajouter un paramètre</div>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label for="param_nom" class="form-label small fw-semibold">Nom</label>
                        <input type="text" name="nom" id="param_nom" value="{{ old('nom') }}" class="form-control @error('nom') is-invalid @enderror" placeholder="ex. Hémoglobine" required>
                        @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="param_type" class="form-label small fw-semibold">Type de résultat</label>
                        <select name="type" id="param_type" class="form-select" onchange="basculerTypeParametre(this.value)">
                            <option value="numerique" @selected(old('type', 'numerique') === 'numerique')>Valeur chiffrée</option>
                            <option value="texte" @selected(old('type') === 'texte')>Résultat en texte</option>
                        </select>
                    </div>
                    <div class="col-md-4" data-type-parametre="numerique">
                        <label for="param_unite" class="form-label small fw-semibold">Unité</label>
                        <input type="text" name="unite" id="param_unite" value="{{ old('unite') }}" class="form-control" placeholder="ex. g/dL">
                    </div>
                    <div class="col-md-3" data-type-parametre="numerique">
                        <label for="param_min" class="form-label small fw-semibold">Valeur normale min.</label>
                        <input type="text" inputmode="decimal" name="valeur_min" id="param_min" value="{{ old('valeur_min') }}" class="form-control @error('valeur_min') is-invalid @enderror" placeholder="ex. 12">
                        @error('valeur_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3" data-type-parametre="numerique">
                        <label for="param_max" class="form-label small fw-semibold">Valeur normale max.</label>
                        <input type="text" inputmode="decimal" name="valeur_max" id="param_max" value="{{ old('valeur_max') }}" class="form-control @error('valeur_max') is-invalid @enderror" placeholder="ex. 16">
                        @error('valeur_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12" data-type-parametre="numerique">
                        <details @if (old('valeur_min_homme') || old('valeur_min_femme') || old('valeur_min_enfant')) open @endif>
                            <summary class="small fw-semibold text-teal" style="cursor: pointer;">Valeurs différentes selon le sexe ou l'âge (facultatif)</summary>
                            <div class="row g-2 mt-1">
                                @foreach (['homme' => 'Homme', 'femme' => 'Femme', 'enfant' => 'Enfant (moins de 15 ans)'] as $profil => $libelle)
                                    <div class="col-md-4">
                                        <div class="small fw-semibold mb-1">{{ $libelle }}</div>
                                        <div class="input-group input-group-sm">
                                            <input type="text" inputmode="decimal" name="valeur_min_{{ $profil }}" value="{{ old('valeur_min_'.$profil) }}" class="form-control @error('valeur_min_'.$profil) is-invalid @enderror" placeholder="min." aria-label="{{ $libelle }} : valeur minimale">
                                            <input type="text" inputmode="decimal" name="valeur_max_{{ $profil }}" value="{{ old('valeur_max_'.$profil) }}" class="form-control @error('valeur_max_'.$profil) is-invalid @enderror" placeholder="max." aria-label="{{ $libelle }} : valeur maximale">
                                        </div>
                                        @error('valeur_max_'.$profil)<div class="small text-danger mt-1">{{ $message }}</div>@enderror
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-text">Laissés vides, ce sont les valeurs générales qui s'appliquent. Les valeurs « enfant » nécessitent la date de naissance du patient.</div>
                        </details>
                    </div>
                    <div class="col-md-6" data-type-parametre="texte">
                        <label for="param_texte" class="form-label small fw-semibold">Résultat normal</label>
                        <input type="text" name="valeur_normale_texte" id="param_texte" value="{{ old('valeur_normale_texte') }}" class="form-control" placeholder="ex. Négatif">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-teal fw-semibold">Ajouter le paramètre</button>
                    </div>
                </div>
            </form>

            @push('scripts')
                <script>
                    function basculerTypeParametre(type) {
                        document.querySelectorAll('#form-parametre [data-type-parametre]').forEach(function (bloc) {
                            bloc.hidden = bloc.dataset.typeParametre !== type;
                        });
                    }
                    basculerTypeParametre(document.getElementById('param_type').value);
                </script>
            @endpush
        @endif
    </div>
</div>
