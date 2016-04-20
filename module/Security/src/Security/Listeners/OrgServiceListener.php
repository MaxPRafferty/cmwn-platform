<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Org\Service\OrganizationServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class OrgServiceListener
 */
class OrgServiceListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            OrganizationServiceInterface::class,
            'fetch.all.orgs',
            [$this, 'fetchAll']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(OrganizationServiceInterface::class, $listener);
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
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        if ($this->getRbac()->isGranted($user->getRole(), 'view.all.orgs')) {
            return;
        }

        $event->stopPropagation(true);
        /** @var OrganizationServiceInterface $service */
        $service = $event->getTarget();
        return $service->fetchAllForUser(
            $user,
            $event->getParam('where'),
            $event->getParam('paginate'),
            $event->getParam('prototype')
        );
    }
}
