<?php


namespace Banana\Template;


use Banana\AppContext;
use Banana\Routing\IRouteEndpoint;
use Banana\Routing\Route;
use Banana\Template\Render\IRenderHandler;
use Banana\Template\Render\IRenderPass;
use Banana\Template\Render\RenderContext;

class TemplateManager implements IRouteEndpoint
{
    const IDENTIFIER_PATTERN = "([a-z\d.\\-_]+)";

    private array $params;

    /** @var ?IRenderHandler[] */
    private array $controllers;

    /** @var IRenderPass[] */
    private array $renderPasses;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->controllers = [];
        $this->renderPasses = [];
    }

    /**
     * Load a template
     *
     * @param string $name
     * @return Template The loaded view
     * @throws TemplateNotFoundException
     */
    public function load(string $name): Template
    {
        $viewContentPath = VIEWS_PATH . str_replace('.', DIRECTORY_SEPARATOR, $name) . ".html";
        if (!array_key_exists($name, $this->controllers) || !file_exists($viewContentPath))
            throw new TemplateNotFoundException($name, "Template not found");

        $controller = $this->controllers[$name];
        $viewContent = file_get_contents($viewContentPath);
        return new Template($name, $viewContent, $controller);
    }

    /**
     * @param string $name
     * @param RenderContext $context
     * @param Template $template
     * @return string
     */
    public function render(string $name, RenderContext $context, Template $template): string
    {
        $controller = $template->getController();
        if (isset($controller))
            $controller->onRender($context);

        $content = $template->getContent();
        foreach ($this->renderPasses as $renderPass) {
            $content = $renderPass->execute($name, $context, $template, $content);
        }
        return $content;
    }

    /**
     * @param AppContext $context
     * @param Route $route
     * @param array $parameters
     * @return string
     * @throws TemplateNotFoundException
     */
    public function handle(AppContext $context, Route $route, array $parameters): string
    {
        $templateName = $route->getName();
        $lang = $context->getLang();

        $template = $this->load($templateName);

        $context = new RenderContext(
            $context,
            $route->getRouter(),
            $this->params
        );
        $context->append([
            'page' => [
                'name' => $templateName,
                'lang' => $lang
            ],
            "route" => $parameters
        ]);
        $viewContent = $this->render($templateName, $context, $template);

        $context->append([
            "page" => [
                "content" => $viewContent
            ]
        ]);

        // load the full layout template, render it and then return the rendered template
        $appView = $this->load("layout");
        return $this->render($templateName, $context, $appView);
    }

    /**
     * @param string $template
     * @param IRenderHandler|null $controller
     * @throws TemplateException
     */
    public function registerTemplate(string $template, ?IRenderHandler $controller = null)
    {
        if (!preg_match('/^' . self::IDENTIFIER_PATTERN . '$/', $template))
            throw new TemplateException("Invalid template name");
        $this->controllers[$template] = $controller;
    }

    public function addPass(IRenderPass $pass)
    {
        $this->renderPasses[] = $pass;
    }
}