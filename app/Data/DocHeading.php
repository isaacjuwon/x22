<?php

declare(strict_types=1);

namespace App\Data;

final readonly class DocHeading
{
    public function __construct(
        public string $text,
        public string $slug,
        public int $level,
    ) {}
}
