<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Group\Service\GroupServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\SecurityUser;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class GroupServiceListener
 *
 * ${CARET}
 */
class GroupServiceListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            GroupServiceInterface::class,
            'fetch.all.groups',
            [$this, 'fetchAll']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(GroupServiceInterface::class, $listener);
        }
    }

    /**
     * @param Event $event
     * @return \Zend\Db\ResultSet\HydratingResultSet|\Zend\Paginator\Adapter\DbSelect
     * @throws NotAuthorizedException
     */
    public function fetchAll(Event $event)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $user */
        $user = $this->getAuthenticationService()->getIdentity();
        if ($this->getRbac()->isGranted($user->getRole(), 'view.all.groups')) {
            return;
        }

        $event->stopPropagation(true);
        /** @var GroupServiceInterface $service */
        $service = $event->getTarget();
        return $service->fetchAllForUser(
            $user,
            $event->getParam('where'),
            $event->getParam('paginate'),
            $event->getParam('prototype')
        );
    }
}