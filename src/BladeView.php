<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade;

use Illuminate\View\Factory;
use Spiral\Views\ViewInterface;

final class BladeView implements ViewInterface
{
    public function __construct(
        private readonly Factory $factory,
        private readonly string $filepath
    ) {
    }

    public function render(array $data = []): string
    {
        return $this->factory->make($this->filepath)
            ->with($data)
            ->render();
    }
}
