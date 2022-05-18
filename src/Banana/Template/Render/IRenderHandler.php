<?php

namespace Banana\Template\Render;

interface IRenderHandler
{
    /**
     * @param RenderContext $context
     * @return void
     */
    function onRender(RenderContext $context): void;
}