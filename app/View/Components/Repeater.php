<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Repeater
{
    /** @var array<string, array<string, mixed>> */
    public array $items = [];

    public array $factory = [];

    public static function mount(int $count = 1, array|callable $factory = []): self
    {
        $instance = new self();

        $resolver = is_callable($factory) ? $factory : fn() => $factory;

        $instance->factory = $resolver();

        for ($i = 0; $i < $count; $i++) {
            $uuid = (string) Str::uuid();
            $instance->items[$uuid] = $resolver(); // fresh per item
        }

        return $instance;
    }

    public static function from(array $state): self
    {
        $instance = new self();
        $instance->items     = $state['items'];
        $instance->factory = $state['factory'];

        return $instance;
    }

    public function add(): string
    {
        $uuid = (string) Str::uuid();
        $this->items[$uuid] = $this->factory;

        return $uuid;
    }

    public function delete(string $uuid): void
    {
        unset($this->items[$uuid]);
    }

    public function tap(string $uuid, array $overrides): void
    {
        if (array_key_exists($uuid, $this->items)) {
            $this->items[$uuid] = array_merge($this->items[$uuid], $overrides);
        }
    }

    public function duplicate(string $uuid): ?string
    {
        if (! array_key_exists($uuid, $this->items)) {
            return null;
        }

        $newUuid = (string) Str::uuid();
        $newItems = [];

        foreach ($this->items as $key => $value) {
            $newItems[$key] = $value;

            if ($key === $uuid) {
                $newItems[$newUuid] = $this->items[$uuid];
            }
        }

        $this->items = $newItems;

        return $newUuid;
    }

    public function all(): array
    {
        return $this->items;
    }

    public function values(): array
    {
        return array_values($this->items);
    }

    public function collection(): Collection
    {
        return collect($this->values());
    }

    public function count(): int
    {
        return count($this->items);
    }


    public function getItem(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    public function setItem(string $key, mixed $value): void
    {
        if (array_key_exists($key, $this->items) && is_array($value)) {

            $this->items[$key] = array_merge($this->items[$key], $value);
        }
    }
}
