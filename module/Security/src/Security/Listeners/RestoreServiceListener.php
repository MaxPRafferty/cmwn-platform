<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use RestoreDb\Service\RestoreDbServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class RestoreServiceListener
 * @package Security\Listeners
 */
class RestoreServiceListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var RestoreDbServiceInterface
     */
    protected $restoreService;

    /**
     * @var array
     */
    protected $listeners;

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            RestoreDbServiceInterface::class,
            'Restore.db.state',
            [$this, 'restoreDbState']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        $manager->detach(RestoreDbServiceInterface::class, $this->listeners[0]);
    }

    /**
     * @throws NotAuthorizedException
     */
    public function restoreDbState()
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        if (!$this->getRbac()->isGranted($user->getRole(), 'Restore.db.state')) {
            throw new NotAuthorizedException;
        }
    }
}
