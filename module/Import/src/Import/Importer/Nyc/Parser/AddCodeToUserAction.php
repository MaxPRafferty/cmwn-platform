<?php

namespace Import\Importer\Nyc\Parser;

use Import\ActionInterface;
use Security\Service\SecurityServiceInterface;
use User\UserAwareInterface;

/**
 * Class AddCodeToUserAction
 */
class AddCodeToUserAction implements ActionInterface
{
    /**
     * @var UserAwareInterface
     */
    protected $user;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var string
     */
    protected $code;

    /**
     * AddCodeToUserAction constructor.
     *
     * @param UserAwareInterface $user
     * @param SecurityServiceInterface $securityService
     * @param string $code
     */
    public function __construct(UserAwareInterface $user, SecurityServiceInterface $securityService, $code)
    {
        $this->user            = $user;
        $this->securityService = $securityService;
        $this->code            = $code;
    }

    /**
     * Make this pretty human readable so we can understand what is going on
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            'Setting code to user "%s"',
            $this->user->getUser()
        );
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $this->securityService->saveCodeToUser($this->code, $this->user->getUser(), 30);
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 2;
    }
}
