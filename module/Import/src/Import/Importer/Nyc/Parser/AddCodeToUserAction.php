<?php

namespace Import\Importer\Nyc\Parser;

use Application\Utils\Date\DateTimeFactory;
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
     * @var \DateTime
     */
    protected $codeStart;

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
        $this->codeStart       = DateTimeFactory::factory('now');
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
     * @param \DateTime $start
     */
    public function setCodeStart(\DateTime $start)
    {
        $this->codeStart = $start;
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $this->securityService->saveCodeToUser($this->code, $this->user->getUser(), 30, $this->codeStart);
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
