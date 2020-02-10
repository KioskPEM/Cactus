<?php


namespace Cactus\Controller\User;


use Cactus\Template\Controller\ITemplateController;
use Cactus\Template\Render\RenderContext;
use Cactus\User\User;
use Cactus\User\UserManager;

class UserProfileController implements ITemplateController
{
    private User $user;

    /**
     * @inheritDoc
     */
    function onRender(RenderContext $context): void
    {
        $userId = $context->param("user_id");
        $userManager = UserManager::Instance();
        $userManager->loginUser($userId);
    }

    function get_first_name(): string
    {
        return $this->user->getFirstName();
    }

    function get_last_name(): string
    {
        return $this->user->getLastName();
    }
}