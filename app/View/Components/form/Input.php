<?php

namespace App\View\Components\form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public string $type = 'text',
        public ?string $value = null,
        public ?string $label = null,
        public ?string $placeholder = null,
        public ?string $icon = null,
        public ?string $prefix = null,
        public ?string $suffix = null,
        public array|Collection $options = [],
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public int $rows = 3,
        public ?string $min = null,
        public ?string $max = null,
        public ?string $step = null,
        public ?string $help = null,
        public ?string $id = null,
        public string $containerClass = 'mb-3',
    ) {
        if ($this->options instanceof Collection) {
            $this->options = $this->options->toArray();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
