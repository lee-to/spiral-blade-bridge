<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade\Bootloader;

use Leeto\SpiralBlade\BladeEngine;
use Leeto\SpiralBlade\Config\BladeConfig;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Views\Bootloader\ViewsBootloader;

final class BladeBootloader extends Bootloader
{
    protected const SINGLETONS = [
        BladeEngine::class => [self::class, 'bladeEngine'],
    ];

    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(ViewsBootloader $views): void
    {
        $this->config->setDefaults(BladeConfig::CONFIG, [
            'paths' => [
                'app' => directory('root') . 'app/views',
            ],
            'cache_dir' => directory('runtime') . 'cache/views',
            'component_namespaces' => [],
            'anonymous_component_namespaces' => [],
            'directives' => [],
        ]);

        $views->addEngine(BladeEngine::class);
    }

    private function bladeEngine(
        BladeConfig $config,
    ): BladeEngine {
        return new BladeEngine($config);
    }
}
