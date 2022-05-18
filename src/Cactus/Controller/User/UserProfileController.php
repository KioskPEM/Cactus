<?php


namespace Cactus\Controller\User;


use Banana\Template\Render\IRenderHandler;
use Banana\Template\Render\RenderContext;
use Cactus\User\Exception\UserException;
use Cactus\User\User;
use Cactus\User\UserManager;

class UserProfileController implements IRenderHandler
{
    private User $user;

    /**
     * @inheritDoc
     * @throws UserException
     */
    function onRender(RenderContext $context): void
    {
        //TODO: UserManager
        $userId = intval($context->param("route.user_id"));
        $userManager = UserManager::Instance();
        $this->user = $userManager->loginUser($userId);
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