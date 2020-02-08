<?php


namespace Cactus\Controller\EasterEgg;


use Cactus\EasterEgg\Jukebox;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class FlappyBirdController implements ITemplateController
{
    /**
     * @inheritDoc
     */
    function onRender(RenderContext $context): void
    {
        $jukebox = Jukebox::Instance();
        $jukebox->playIndexed(6);
    }
}