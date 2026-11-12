{{-- Compte de connexion et service principal : donnent au médecin accès à sa file d'attente --}}
@php($medecinCourant = $medecin ?? null)
<div class="col-md-6">
    <label for="user_id" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
        <i data-lucide="key-round" class="lucide-sm text-primary"></i> Compte de connexion
    </label>
    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
        <option value="">Aucun compte relié</option>
        @foreach ($comptes as $compte)
            <option value="{{ $compte->id }}" @selected((string) old('user_id', $medecinCourant?->user_id) === (string) $compte->id)>
                {{ $compte->prenom }} {{ $compte->nom }}{{ $compte->email ? ' · '.$compte->email : ($compte->telephone ? ' · '.$compte->telephone : '') }}
            </option>
        @endforeach
    </select>
    @error('user_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @else
        <div class="form-text">Le compte « Médecin » avec lequel ce praticien se connecte. Sans compte relié, il n'a pas de file d'attente.</div>
    @enderror
</div>

<div class="col-md-6">
    <label for="service_id" class="form-label fw-bold text-dark small d-flex align-items-center gap-1">
        <i data-lucide="building-2" class="lucide-sm text-primary"></i> Service principal
    </label>
    <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror">
        <option value="">Aucun service</option>
        @foreach ($services as $service)
            <option value="{{ $service->id }}" @selected((string) old('service_id', $medecinCourant?->service_id) === (string) $service->id)>
                {{ $service->nom }}
            </option>
        @endforeach
    </select>
    @error('service_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @else
        <div class="form-text">Les tickets payés de ce service sans médecin attribué lui seront proposés.</div>
    @enderror
</div>
