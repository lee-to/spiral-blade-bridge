<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade;

use Closure;

interface DirectiveInterface
{
    public function getType(): DirectiveType;

    public function getName(): string;

    public function handler(): Closure;
}
