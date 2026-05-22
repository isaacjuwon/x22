<?php

declare(strict_types=1);

namespace App\View\Components;

use Livewire\Form;

class Step
{
    public function __construct(
        public readonly string $key,
        public readonly Form $form,
        public readonly string $view,
        public bool $skippable = false,
        public bool $validate = true,
    ) {}

    public function skippable()
    {
        $this->skippable = true;
    }


    public function statePath(): string
    {
        return $this->form->getPropertyName();
    }

    public function view(): string
    {
        return $this->view;
    }

    // only validate when there is
    // rules on the form object
    public function validate(): void
    {
        if ($this->form->getRules()) {
            $this->form->validate();
        }
    }

    public function all(): array
    {
        return $this->form->all();
    }
}
