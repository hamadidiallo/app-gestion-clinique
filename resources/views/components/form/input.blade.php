@props([
    'type' => 'text',
    'name' => '',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'icon' => null,
    'prefix' => null,
    'suffix' => null,
    'options' => null,
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'rows' => 3,
    'min' => null,
    'max' => null,
    'step' => null,
    'help' => null,
    'id' => null,
    'containerClass' => 'mb-3',
])

@php
    $type = $type ?? 'text';
    $label = $label ?? null;
    $value = $value ?? null;
    $placeholder = $placeholder ?? null;
    $icon = $icon ?? null;
    $prefix = $prefix ?? null;
    $suffix = $suffix ?? null;
    $options = $options ?? [];
    $required = $required ?? false;
    $readonly = $readonly ?? false;
    $disabled = $disabled ?? false;
    $rows = $rows ?? 3;
    $min = $min ?? null;
    $max = $max ?? null;
    $step = $step ?? null;
    $help = $help ?? null;
    $id = $id ?? null;
    $containerClass = $containerClass ?? 'mb-3';

    $attributes = $attributes ?? new \Illuminate\View\ComponentAttributeBag();

    $inputId = $id ?? $name;
    $inputValue = old($name, $value ?? '');
    $isInvalid = $errors->has($name);
    
    // Normalisation du nom de l'icône (ajoute 'bi-' si manquant)
    $iconClass = null;
    if ($icon) {
        $iconClass = str_starts_with($icon, 'bi-') ? $icon : 'bi-' . $icon;
    }

    $isGroup = $iconClass || $prefix || $suffix || $type === 'password';
@endphp

<div class="{{ $containerClass }}">
    {{-- Libellé du champ --}}
    @if($label)
        <label for="{{ $inputId }}" class="form-label fw-bold text-dark fs-7 mb-1 d-flex align-items-center justify-content-between">
            <span>
                {{ $label }}
                @if($required)
                    <span class="text-danger ms-1" title="Champ obligatoire">*</span>
                @endif
            </span>
        </label>
    @endif

    {{-- Type Textarea --}}
    @if ($type === 'textarea')
        <div class="{{ $iconClass ? 'input-group' : '' }}">
            @if($iconClass)
                <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi {{ $iconClass }}"></i>
                </span>
            @endif
            <textarea 
                name="{{ $name }}" 
                id="{{ $inputId }}" 
                rows="{{ $rows }}"
                class="form-control @if($isInvalid) is-invalid @endif @if($iconClass) border-start-0 @endif" 
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}>{{ $inputValue }}</textarea>
        </div>

    {{-- Type Select --}}
    @elseif ($type === 'select')
        <div class="{{ $isGroup ? 'input-group' : '' }}">
            @if($iconClass)
                <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi {{ $iconClass }}"></i>
                </span>
            @endif
            @if($prefix)
                <span class="input-group-text bg-light text-muted">{{ $prefix }}</span>
            @endif
            
            <select 
                name="{{ $name }}" 
                id="{{ $inputId }}" 
                class="form-select @if($isInvalid) is-invalid @endif @if($iconClass) border-start-0 @endif"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}>
                @if($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif
                @foreach ($options as $key => $optionLabel)
                    <option value="{{ $key }}" {{ (string)$inputValue === (string)$key ? 'selected' : '' }}>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>

            @if($suffix)
                <span class="input-group-text bg-light text-muted fw-semibold">{{ $suffix }}</span>
            @endif
        </div>

    {{-- Type Checkbox / Radio --}}
    @elseif (in_array($type, ['checkbox', 'radio']))
        <div class="form-check">
            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $inputId }}" 
                value="{{ $value ?? 1 }}"
                class="form-check-input @if($isInvalid) is-invalid @endif"
                {{ $inputValue ? 'checked' : '' }}
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}>
            @if($label)
                <label class="form-check-label fw-semibold text-dark" for="{{ $inputId }}">
                    {{ $label }}
                </label>
            @endif
        </div>

    {{-- Types Standard (text, email, password, number, date, time, etc.) --}}
    @else
        <div class="{{ $isGroup ? 'input-group' : '' }}">
            @if($iconClass)
                <span class="input-group-text bg-light text-muted border-end-0">
                    <i class="bi {{ $iconClass }}"></i>
                </span>
            @endif
            @if($prefix)
                <span class="input-group-text bg-light text-muted">{{ $prefix }}</span>
            @endif

            <input 
                type="{{ $type }}" 
                name="{{ $name }}" 
                id="{{ $inputId }}" 
                value="{{ $inputValue }}"
                class="form-control @if($isInvalid) is-invalid @endif @if($iconClass) border-start-0 @endif" 
                placeholder="{{ $placeholder }}"
                @if($min !== null) min="{{ $min }}" @endif
                @if($max !== null) max="{{ $max }}" @endif
                @if($step !== null) step="{{ $step }}" @endif
                {{ $required ? 'required' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes }}>

            @if($type === 'password')
                <button type="button" 
                        class="btn btn-outline-secondary border-start-0" 
                        tabindex="-1"
                        onclick="togglePasswordVisibility('{{ $inputId }}', this)">
                    <i class="bi bi-eye"></i>
                </button>
            @endif

            @if($suffix)
                <span class="input-group-text bg-light text-muted fw-semibold">{{ $suffix }}</span>
            @endif
        </div>
    @endif

    {{-- Message d'aide sous l'input --}}
    @if($help && !$isInvalid)
        <div class="form-text text-muted small mt-1">
            <i class="bi bi-info-circle me-1"></i>{{ $help }}
        </div>
    @endif

    {{-- Message d'erreur de validation --}}
    @error($name)
        <div class="invalid-feedback d-block mt-1">
            <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
        </div>
    @enderror
</div>

@once
    @push('scripts')
        <script>
            function togglePasswordVisibility(inputId, btn) {
                const input = document.getElementById(inputId);
                const icon = btn.querySelector('i');
                if (input && icon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                }
            }
        </script>
    @endpush
@endonce
