<?php

namespace Cactus\Template\Render\Pass;


use Cactus\Template\Render\RenderContext;
use Cactus\Template\Template;

interface IRenderPass
{
    /**
     * @param string $name
     * @param RenderContext $context
     * @param Template $template
     * @param string $subject
     * @return string
     */
    function execute(string $name, RenderContext $context, Template $template, string $subject): string;
}