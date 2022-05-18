<?php


namespace Banana\Template\Render;

use Banana\AppContext;
use Banana\Collection\ArrayPath;
use Banana\Routing\Router;

class RenderContext
{
    private AppContext $context;
    private Router $router;
    private array $params;

    /**
     * RenderContext constructor.
     * @param AppContext $context
     * @param Router $router
     * @param array $params
     */
    public function __construct(AppContext $context, Router $router, array $params = [])
    {
        $this->context = $context;
        $this->router = $router;
        $this->params = $params;
    }

    public function hasParam(string $key): bool
    {
        return ArrayPath::has($this->params, $key);
    }

    public function param(string $key): string
    {
        return ArrayPath::get($this->params, $key);
    }

    public function append(array $other): void
    {
        $this->params = array_replace_recursive($this->params, $other);
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return AppContext
     */
    public function getContext(): AppContext
    {
        return $this->context;
    }
}