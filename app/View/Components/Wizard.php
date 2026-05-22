<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Collection;

class Wizard
{
    protected array $views = [];

    /** @var Collection<string, Step> */
    private Collection $steps;

    private string $currentKey = '';

    /** @var array<string> */
    private array $completed = [];

    /** @var array<string, array{skippable: bool}> — persisted step registry */
    private array $registry = [];

    public function __construct(Step ...$steps)
    {
        $this->steps = collect($steps)->keyBy(fn(Step $s) => $s->key);

        if ($this->steps->isNotEmpty()) {
            $this->currentKey = $this->steps->keys()->first();
            $this->buildRegistry();
        }
    }

    public static function from(array $state): self
    {
        $instance = new self();

        $instance->currentKey = $state['currentKey'] ?? '';
        $instance->completed = $state['completed'] ?? [];
        $instance->registry = $state['registry'] ?? [];

        return $instance;
    }

    public function currentForm(): \Livewire\Form
    {
        return $this->currentStep()->form;
    }

    public function withSteps(Step ...$steps): void
    {
        $this->steps = collect($steps)->keyBy(fn(Step $s) => $s->key);

        if (empty($this->registry)) {
            $this->buildRegistry();
        }

        // First boot: currentKey not set yet
        if ($this->currentKey === '') {
            $this->currentKey = $this->steps->keys()->first();
        }
    }

    public function getState(): array
    {
        return [
            'currentKey' => $this->currentKey,
            'completed' => $this->completed,
            'registry' => $this->registry,
        ];
    }

    public function currentStep(): Step
    {
        return $this->steps->get($this->currentKey);
    }

    public function currentKey(): string
    {
        return $this->currentKey;
    }

    public function steps(): Collection
    {
        return $this->steps;
    }

    public function completed(): array
    {
        return $this->completed;
    }


    public function registry(): array
    {
        return $this->registry;
    }

    public function isActive(string $key): bool
    {
        return $key === $this->currentKey;
    }

    public function isCompleted(string $key): bool
    {
        return in_array($key, $this->completed);
    }

    public function isFirst(): bool
    {
        return $this->currentKey === array_key_first($this->registry);
    }

    public function isLast(): bool
    {
        return $this->currentKey === array_key_last($this->registry);
    }

    public function isSkippable(string $key): bool
    {
        return $this->registry[$key]['skippable'] ?? false;
    }

    public function goTo(string $key): void
    {
        if (! array_key_exists($key, $this->registry)) {
            return;
        }

        $keys = array_keys($this->registry);
        $targetIndex = array_search($key, $keys, true);
        $currentIndex = array_search($this->currentKey, $keys, true);

        if ($targetIndex < $currentIndex || in_array($key, $this->completed, true)) {
            $this->currentKey = $key;
        }
    }

    public function next(bool $validate = true): void
    {

        if ($validate && $this->currentStep()->validate) {
            $this->currentStep()->validate();
        }

        $keys = array_keys($this->registry);

        $index = array_search($this->currentKey, $keys, true);

        if (isset($keys[$index + 1])) {

            if (! in_array($this->currentKey, $this->completed, true)) {
                $this->completed[] = $this->currentKey;
            }

            $this->currentKey = $keys[$index + 1];
        }
    }

    public function previous(): void
    {
        $keys = array_keys($this->registry);
        $index = array_search($this->currentKey, $keys, true);

        if ($index > 0) {
            $this->completed = array_values(
                array_filter($this->completed, fn(string $k) => $k !== $keys[$index - 1])
            );

            $this->currentKey = $keys[$index - 1];
        }
    }

    public function skip(): void
    {
        if ($this->isSkippable($this->currentKey) && ! $this->isLast()) {
            $this->next(false);
        }
    }

    public function all(): array
    {
        return $this->steps
            ->map(fn(Step $step) => $step->all())
            ->all();
    }

    private function buildRegistry(): void
    {
        $this->registry = $this->steps
            ->map(fn(Step $s) => ['skippable' => $s->skippable])
            ->all();
    }
}
