<?php

namespace Banana\Template\Render\Pass;

use Banana\Localization\LocalizationManager;
use Banana\Template\Render\RenderContext;
use Banana\Template\Template;
use Banana\Template\TemplateManager;

class LocalizePass implements IRenderPass
{
    const PATTERN = '/%\{' . TemplateManager::IDENTIFIER_PATTERN . '}/';

    private LocalizationManager $localizationManager;

    public function __construct(LocalizationManager $localizationManager)
    {
        $this->localizationManager = $localizationManager;
    }

    /**
     * @inheritDoc
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string
    {
        return preg_replace_callback(static::PATTERN, function ($matches) use ($name, $context) {
            $key = $matches[1];
            if (strpos($key, "page.") === 0)
                $key = "pages." . $name . substr($key, 4);

            $appContext = $context->getContext();
            $lang = $appContext->getLang();
            return $this->localizationManager->translate($lang, $key);
        }, $subject);
    }
}