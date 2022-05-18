<?php

namespace Banana\Template\Render\Pass;


use Banana\Template\Render\RenderContext;
use Banana\Template\Template;

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