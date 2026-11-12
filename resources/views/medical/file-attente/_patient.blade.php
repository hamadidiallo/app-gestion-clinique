{{-- Une ligne de la file d'attente : un ticket payé dont la consultation n'est pas encore ouverte --}}
@php
    $consultationAccueil = $ticket->consultation;
    $constantesPrises = $consultationAccueil && collect(['tension_arterielle', 'temperature', 'poids', 'pouls', 'glycemie', 'saturation_oxygene'])
        ->contains(fn ($champ) => filled($consultationAccueil->{$champ}));
    $arrivee = \Carbon\Carbon::parse($ticket->date_ticket);
    $minutes = (int) $arrivee->diffInMinutes(now());
    $attenteLongue = $minutes >= 45;
@endphp
<div class="d-flex flex-wrap align-items-center gap-3 py-3 border-bottom">
    @if ($position)
        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold font-mono flex-shrink-0" style="width: 38px; height: 38px; background: {{ $position === 1 ? '#0f766e' : '#e2f1ef' }}; color: {{ $position === 1 ? '#fff' : '#0f766e' }};" aria-label="Position {{ $position }} dans la file">{{ $position }}</div>
    @endif

    <div class="flex-grow-1" style="min-width: 200px;">
        <div class="fw-bold text-dark">
            {{ $ticket->patient ? mb_strtoupper($ticket->patient->nom).' '.$ticket->patient->prenom : 'Patient anonyme' }}
        </div>
        <div class="small text-muted d-flex flex-wrap gap-2 align-items-center">
            @if ($ticket->patient?->reference)<span class="font-mono">{{ $ticket->patient->reference }}</span>@endif
            <span>{{ $ticket->service?->nom ?? 'Consultation' }}</span>
            @if ($constantesPrises)
                <span class="badge rounded-pill" style="background: #e2f1ef; color: #0f766e;">Constantes prises</span>
            @endif
        </div>
    </div>

    <div class="text-end small">
        <div class="font-mono">Arrivé à {{ $arrivee->format('H:i') }}</div>
        <div class="{{ $attenteLongue ? 'text-danger fw-semibold' : 'text-muted' }}">
            attend depuis {{ $minutes < 60 ? $minutes.' min' : intdiv($minutes, 60).' h '.str_pad($minutes % 60, 2, '0', STR_PAD_LEFT) }}
        </div>
    </div>

    <form method="POST" action="{{ route('medecin.prendre', $ticket) }}" class="m-0">
        @csrf
        <button type="submit" class="btn btn-teal fw-semibold d-inline-flex align-items-center gap-1">
            <i data-lucide="stethoscope" class="lucide-sm"></i>
            <span>Prendre en charge</span>
        </button>
    </form>
</div>
