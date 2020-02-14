<?php


namespace Cactus\Controller\EasterEgg;


use Cactus\EasterEgg\Jukebox;
use Cactus\Exception\FileException;
use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;

class FlappyBirdController implements ITemplateController
{
    /**
     * @inheritDoc
     * @throws FileException
     */
    function onRender(RenderContext $context): void
    {
        $jukebox = Jukebox::Instance();
        $jukebox->play(6);
    }
}