<?php

declare(strict_types=1);

namespace Leeto\SpiralBlade;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Leeto\SpiralBlade\Config\BladeConfig;
use Spiral\Views\ContextInterface;
use Spiral\Views\EngineInterface;
use Spiral\Views\LoaderInterface;
use Spiral\Views\ViewInterface;

final class BladeEngine implements EngineInterface
{
    private ?LoaderInterface $loader = null;

    private readonly CompilerEngine $blade;

    private Factory $factory;

    public function __construct(BladeConfig $config)
    {
        $paths = $config->getPaths();
        $cachePath = $config->getCacheDir();

        $compiler = new BladeCompiler(
            files: new Filesystem(),
            cachePath: $cachePath,
        );

        foreach ($config->getComponentNamespaces() as $namespace => $prefix) {
            $compiler->componentNamespace($namespace, $prefix);
        }

        foreach ($config->getAnonymousComponentNamespaces() as $directory => $prefix) {
            $compiler->anonymousComponentNamespace($directory, $prefix);
        }

        foreach ($config->getDirectives() as $class) {
            $this->addDirectives($compiler, $class);
        }

        $this->blade = new CompilerEngine(
            compiler: $compiler,
            files: new Filesystem()
        );

        $engines = new EngineResolver();
        $engines->register('blade', fn () => $this->blade);

        $this->factory = new Factory(
            engines: $engines,
            finder: new FileViewFinder(
                files: new Filesystem(),
                paths: $paths,
                extensions: ['blade.php']
            ),
            events: new Dispatcher(),
        );

        foreach ($paths as $namespace => $path) {
            $this->factory = $this->factory->addNamespace($namespace, $path);
        }

        Container::getInstance()->bind(ViewFactory::class, fn () => $this->factory);
        Container::getInstance()->bind(View::class, fn () => $this->factory);
        Container::getInstance()->bind('view', View::class);
        Container::getInstance()->bind(Application::class, fn () => new class {
            public function getNamespace(): string
            {
                return 'App';
            }
        });
    }

    public function withLoader(LoaderInterface $loader): EngineInterface
    {
        $engine = clone $this;
        $engine->loader = $loader->withExtension('blade.php');

        return $engine;
    }

    public function getLoader(): LoaderInterface
    {
        return $this->loader;
    }

    public function compile(string $path, ContextInterface $context): mixed
    {
        $filepath = $this->getLoader()
            ->load($this->normalize($path))
            ->getFilename();

        $this->blade->getCompiler()->compile($filepath);

        return $filepath;
    }

    /**
     * @param  BladeCompiler  $compiler
     * @param  class-string<DirectiveInterface>  $class
     * @return BladeCompiler
     */
    protected function addDirectives(BladeCompiler $compiler, string $class): BladeCompiler
    {
        $directive = new $class();

        match ($directive->getType()) {
            DirectiveType::DEFAULT => $compiler
                ->directive($directive->getName(), $directive->handler()),
            DirectiveType::IF => $compiler
                ->if($directive->getName(), $directive->handler()),
            DirectiveType::STRINGABLE => $compiler
                ->stringable($directive->handler()),
        };

        return $compiler;
    }

    protected function normalize(string $path): string
    {
        return str_replace('.', '/', $path);
    }

    public function reset(string $path, ContextInterface $context): void
    {
        $filepath = $this->getLoader()
            ->load($this->normalize($path))
            ->getFilename();

        unlink($this->blade->getCompiler()->getCompiledPath($filepath));
    }

    public function get(string $path, ContextInterface $context): ViewInterface
    {
        return new BladeView($this->factory, $this->normalize($path));
    }
}
