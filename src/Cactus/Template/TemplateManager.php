<?php


namespace Cactus\Template;


use Cactus\Http\HttpCode;
use Cactus\I18n\Exception\LanguageNotFoundException;
use Cactus\I18n\I18nManager;
use Cactus\Routing\IRouteEndpoint;
use Cactus\Routing\Route;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Exception\TemplateNotFoundException;
use Cactus\Template\Pass\IRenderPass;
use Cactus\Template\Render\RenderContext;
use Cactus\Util\ClientRequest;

class TemplateManager implements IRouteEndpoint
{
    const IDENTIFIER_PATTERN = '([\w\.-]+)';

    private array $params;

    private I18nManager $i18nManager;
    private array $templates;
    private array $renderPasses;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->i18nManager = new I18nManager();
        $this->templates = [];
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
        $viewContentPath = VIEWS_PATH . $name . ".html";
        if (!array_key_exists($name, $this->templates) || !file_exists($viewContentPath))
            throw new TemplateNotFoundException($name, "Template not found", HttpCode::CLIENT_NOT_FOUND);

        $controller = $this->templates[$name];
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
        $content = $template->getContent();
        foreach ($this->renderPasses as $renderPass) {
            /* @var $renderPass IRenderPass */
            $content = $renderPass->execute($name, $context, $template, $content);
        }
        return $content;
    }

    /**
     * @param Route $route
     * @param array $parameters
     * @return string
     * @throws TemplateNotFoundException
     * @throws LanguageNotFoundException
     */
    public function handle(Route $route, array $parameters): string
    {
        $request = ClientRequest::Instance();
        $templateName = $route->getName();

        $template = $this->load($templateName);

        $lang = $request->getLang();
        $i18n = $this->i18nManager->load($lang);

        // set-up the render context
        $context = new RenderContext(
            $i18n,
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

    public function registerTemplate(string $route, ?ITemplateController $controller = null)
    {
        $this->templates[$route] = $controller;
    }

    public function addPass(IRenderPass $pass)
    {
        $this->renderPasses[] = $pass;
    }
}