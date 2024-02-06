# Spiral Framework: Blade Adapter

## Installation

```
composer require lee-to/spiral-blade-bridge
```

To enable extension modify your application by adding `Leeto\SpiralBlade\Bootloader\BladeBootloader`:

```php

use Leeto\SpiralBlade\Bootloader\BladeBootloader;

class Kernel extends \Spiral\Framework\Kernel
{
    // ..

    public function defineBootloaders(): array
    {
        return [
            // ..
            // Views
            BladeBootloader::class,
            // ..
        ];
    }

    // ..
}
```

## Configuration

Publish the config to `app/config/views/blade.php`
and you can add your class components and anonymous ones as well as directives

```php
return [
    'paths' => [
        // 'custom' => directory('root') . 'packages/custom/views',
        'app' => directory('root') . 'app/views',
    ],
    'cache_dir' => directory('runtime') . 'cache/views',
    'component_namespaces' => [
        // 'VendorName\Components' => 'prefix'
    ],
    'anonymous_component_namespaces' => [
        // directory('root') . 'packages/prefix/views/components' => 'prefix',
    ],
    'directives' => [
        // MyCustomDirective::class,
    ],
];
```

## Directive

Add a class that implements the Leeto\SpiralBlade\DirectiveInterface interface and add it to the config

```php
<?php

use Closure;
use Leeto\SpiralBlade\DirectiveInterface;
use Leeto\SpiralBlade\DirectiveType;

final class DateTimeDirective implements DirectiveInterface
{
    public function getType(): DirectiveType
    {
        return DirectiveType::DEFAULT;
    }

    public function getName(): string
    {
        return 'datetime';
    }

    public function handler(): Closure
    {
        return static fn (string $expression) => "<?php echo ($expression)->format('m/d/Y H:i'); ?>";
    }
}
```

