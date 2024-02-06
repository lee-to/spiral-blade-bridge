<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade;

use Stringable;

enum DirectiveType: string
{
    case DEFAULT = 'directive';

    case IF = 'if';

    case STRINGABLE = 'stringable';
}
