<?php

namespace Security\Listeners;

use Application\Exception\NotAuthorizedException;
use Group\Service\UserGroupService;
use Group\Service\UserGroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use Security\Service\SecurityOrgService;
use Security\Service\SecurityOrgServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class OrgServiceListener
 */
class OrgServiceListener implements RbacAwareInterface, AuthenticationServiceAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var SecurityOrgServiceInterface
     */
    protected $securityOrgService;

    /**
     * @var UserGroupService
     */
    protected $userGroupService;

    /**
     * OrgServiceListener constructor.
     * @param SecurityOrgServiceInterface $securityOrgService
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(
        SecurityOrgServiceInterface $securityOrgService,
        UserGroupServiceInterface $userGroupService
    ) {
        $this->securityOrgService = $securityOrgService;
        $this->userGroupService = $userGroupService;
    }

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

        // TODO I do not think this needs to be the SecurityOrgService it does not dispatch events
        $this->listeners[] = $events->attach(
            SecurityOrgService::class,
            'fetch.org.post',
            [$this, 'fetchOrganization']
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
     * @return \Zend\Db\ResultSet\HydratingResultSet|\Zend\Paginator\Adapter\DbSelect|null
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
            return null;
        }

        $event->stopPropagation(true);

        return $this->userGroupService->fetchOrganizationsForUser(
            $user,
            $event->getParam('prototype')
        );
    }

    /**
     * @param Event $event
     * @throws NotAuthorizedException
     */
    public function fetchOrganization(Event $event)
    {
        $orgId = $event->getParam('org_id', null);
        if (!$this->getAuthenticationService()->hasIdentity()) {
            throw new NotAuthorizedException;
        }

        /** @var SecurityUser $user */
        try {
            $user = $this->getAuthenticationService()->getIdentity();
        } catch (ChangePasswordException $changePassword) {
            $user = $changePassword->getUser();
        }

        $user->setRole($this->securityOrgService->getRoleForOrg($orgId, $user));

        if (!$this->getRbac()->isGranted($user->getRole(), 'view.org')) {
            throw new NotAuthorizedException;
        }
    }
}
