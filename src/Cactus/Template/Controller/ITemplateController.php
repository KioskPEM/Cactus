<?php


namespace Cactus\Template\Controller;

use Cactus\Template\Render\RenderContext;

interface ITemplateController
{
    /**
     * @param RenderContext $context
     * @return void
     */
    function onRender(RenderContext $context): void;
}