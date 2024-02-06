<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade\Config;

use Leeto\SpiralBlade\DirectiveInterface;
use Spiral\Core\InjectableConfig;

final class BladeConfig extends InjectableConfig
{
    public const CONFIG = 'views/blade';

    protected array $config = [
        'paths' => [],
        'cache_dir' => null,
        'component_namespaces' => [],
        'anonymous_component_namespaces' => [],
        'directives' => [],
    ];

    /**
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return $this->config['paths'];
    }

    public function getCacheDir(): string
    {
        return $this->config['cache_dir'];
    }

    /**
     * @return array<string, string>
     */
    public function getComponentNamespaces(): array
    {
        return $this->config['component_namespaces'];
    }

    /**
     * @return array<string, string>
     */
    public function getAnonymousComponentNamespaces(): array
    {
        return $this->config['anonymous_component_namespaces'];
    }

    /**
     * @return array<class-string<DirectiveInterface>>
     */
    public function getDirectives(): array
    {
        return $this->config['directives'];
    }
}
