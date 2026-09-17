@props([
    'currentPeriod' => 'tous',
    'periodLabel' => 'Toutes les périodes',
    'customStart' => request('date_debut'),
    'customEnd' => request('date_fin'),
    'searchValue' => request('q', request('search')),
    'statusValue' => request('statut'),
    'statuses' => null,
    'baseUrl' => request()->url(),
])

@php
    $queryParams = request()->except(['periode', 'date_debut', 'date_fin', 'page', 'q', 'search', 'statut']);
    
    $periods = [
        'tous' => ['label' => 'Tout', 'icon' => 'infinity'],
        'jour' => ['label' => 'Jour', 'icon' => 'calendar'],
        'semaine' => ['label' => 'Semaine', 'icon' => 'calendar-range'],
        'mois' => ['label' => 'Mois', 'icon' => 'calendar-days'],
        'trimestre' => ['label' => 'Trimestre', 'icon' => 'calendar'],
        'semestre' => ['label' => 'Semestre', 'icon' => 'calendar-range'],
        'annuel' => ['label' => 'Annuel', 'icon' => 'calendar'],
    ];

    $hasActiveFilters = $currentPeriod !== 'tous' || !empty($searchValue) || !empty($statusValue) || !empty($customStart) || !empty($customEnd);
@endphp

<div class="card mb-4 bg-white border-0 shadow-sm rounded-3" style="border: 1px solid #e6ebf0 !important;">
    <div class="card-body p-3">
        {{-- Ligne supérieure : Recherche par mot-clé & Filtre par statut --}}
        <form action="{{ $baseUrl }}" method="GET" class="row g-2 mb-3 align-items-center">
            @foreach($queryParams as $paramKey => $paramVal)
                @if(is_array($paramVal))
                    @foreach($paramVal as $arrayVal)
                        <input type="hidden" name="{{ $paramKey }}[]" value="{{ $arrayVal }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                @endif
            @endforeach
            <input type="hidden" name="periode" value="{{ $currentPeriod }}">
            @if($customStart) <input type="hidden" name="date_debut" value="{{ $customStart }}"> @endif
            @if($customEnd) <input type="hidden" name="date_fin" value="{{ $customEnd }}"> @endif

            {{-- Champ de recherche par mot-clé --}}
            <div class="{{ $statuses ? 'col-md-7 col-sm-12' : 'col-md-9 col-sm-12' }}">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted border-end-0">
                        <i data-lucide="search" style="width: 0.95rem; height: 0.95rem;"></i>
                    </span>
                    <input type="text" 
                           name="q" 
                           class="form-control border-start-0 ps-1" 
                           placeholder="Rechercher par référence, nom, matricule ou mot-clé..." 
                           value="{{ $searchValue }}">
                    @if($searchValue)
                        <a href="{{ $baseUrl . '?' . http_build_query(array_merge($queryParams, ['periode' => $currentPeriod])) }}" 
                           class="btn btn-outline-secondary border-start-0 d-flex align-items-center" 
                           title="Effacer la recherche">
                            <i data-lucide="x" style="width: 0.9rem; height: 0.9rem;" class="text-muted"></i>
                        </a>
                    @endif
                    <button type="submit" class="btn btn-teal fw-semibold px-3">
                        Rechercher
                    </button>
                </div>
            </div>

            {{-- Select par Statut (optionnel) --}}
            @if($statuses)
                <div class="col-md-3 col-sm-6">
                    <select name="statut" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Tous les statuts --</option>
                        @foreach($statuses as $statutKey => $statutLabel)
                            <option value="{{ $statutKey }}" {{ (string)$statusValue === (string)$statutKey ? 'selected' : '' }}>
                                {{ $statutLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Bouton de réinitialisation générale --}}
            @if($hasActiveFilters)
                <div class="col-md-2 col-sm-6 text-end">
                    <a href="{{ $baseUrl }}" class="btn btn-sm btn-outline-danger w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="rotate-ccw" style="width: 0.85rem; height: 0.85rem;"></i> Réinitialiser
                    </a>
                </div>
            @endif
        </form>

        <hr class="my-2 opacity-25">

        {{-- Ligne inférieure : Sélection de Période --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-1">
            {{-- Libellé & Badge d'affichage de la période courante --}}
            <div class="d-flex align-items-center gap-2">
                <i data-lucide="calendar" style="width: 1rem; height: 1rem;" class="text-teal"></i>
                <div class="d-flex align-items-center gap-1.5">
                    <span class="text-muted small">Période active :</span>
                    <span class="badge badge-info-pill font-mono">
                        {{ $periodLabel }}
                    </span>
                </div>
            </div>

            {{-- Boutons de sélection rapide de la période --}}
            <div class="d-flex flex-wrap align-items-center gap-1">
                @foreach ($periods as $key => $info)
                    @php
                        $urlParams = array_merge($queryParams, ['periode' => $key]);
                        if ($searchValue) $urlParams['q'] = $searchValue;
                        if ($statusValue) $urlParams['statut'] = $statusValue;
                        $url = $baseUrl . '?' . http_build_query($urlParams);
                        $isActive = ($currentPeriod === $key);
                    @endphp
                    <a href="{{ $url }}" 
                       class="btn btn-sm {{ $isActive ? 'btn-teal fw-semibold shadow-sm' : 'btn-light border text-muted' }} d-inline-flex align-items-center gap-1 rounded-pill px-3">
                        <i data-lucide="{{ $info['icon'] }}" style="width: 0.85rem; height: 0.85rem;"></i>
                        <span>{{ $info['label'] }}</span>
                    </a>
                @endforeach

                {{-- Bouton pour afficher/masquer le filtre par date spécifique --}}
                <button class="btn btn-sm {{ $currentPeriod === 'custom' ? 'btn-teal fw-semibold shadow-sm' : 'btn-light border text-muted' }} rounded-pill px-3 ms-1 d-inline-flex align-items-center gap-1" 
                        type="button" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#customDateFilter" 
                        aria-expanded="{{ $currentPeriod === 'custom' ? 'true' : 'false' }}">
                    <i data-lucide="sliders-horizontal" style="width: 0.85rem; height: 0.85rem;"></i>
                    <span>Personnaliser</span>
                </button>
            </div>
        </div>

        {{-- Formulaire collapsible pour la plage de dates personnalisée --}}
        <div class="collapse {{ $currentPeriod === 'custom' ? 'show' : '' }} mt-3 pt-3 border-top" id="customDateFilter">
            <form action="{{ $baseUrl }}" method="GET" class="row g-2 align-items-end">
                @foreach($queryParams as $paramKey => $paramVal)
                    @if(is_array($paramVal))
                        @foreach($paramVal as $arrayVal)
                            <input type="hidden" name="{{ $paramKey }}[]" value="{{ $arrayVal }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $paramKey }}" value="{{ $paramVal }}">
                    @endif
                @endforeach
                <input type="hidden" name="periode" value="custom">
                @if($searchValue) <input type="hidden" name="q" value="{{ $searchValue }}"> @endif
                @if($statusValue) <input type="hidden" name="statut" value="{{ $statusValue }}"> @endif

                <div class="col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Date de début</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ $customStart }}">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="form-label small fw-semibold text-muted mb-1">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm" value="{{ $customEnd }}">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-teal w-100 fw-semibold d-inline-flex align-items-center justify-content-center gap-1">
                        <i data-lucide="check" style="width: 0.85rem; height: 0.85rem;"></i> Appliquer l'intervalle
                    </button>
                    <a href="{{ $baseUrl }}" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" title="Réinitialiser">
                        <i data-lucide="x" style="width: 0.85rem; height: 0.85rem;"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
