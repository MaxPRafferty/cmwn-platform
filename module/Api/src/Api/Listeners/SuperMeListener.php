<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\V1\Rest\User\MeEntity;
use Group\Service\GroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\Authorization\RbacAwareInterface;
use Security\Authorization\RbacAwareTrait;
use Security\SecurityUser;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class SuperMeListener
 *
 * For users that have the view.all.entities permissions attach all the HAL links for the MeEntity
 */
class SuperMeListener implements AuthenticationServiceAwareInterface, RbacAwareInterface
{
    use RbacAwareTrait;
    use AuthenticationServiceAwareTrait;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    public function __construct(OrganizationServiceInterface $orgService, GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
        $this->orgService   = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'renderGroupHal'], -1000);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'renderOrgHal'], -1000);
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach('ZF\Hal\Plugin\Hal', $listener);
        }
    }

    /**
     * Adds all the group type hal links to the me entity
     *
     * @param Event $event
     */
    public function renderGroupHal(Event $event)
    {
        if (!$this->checkPermission('view.all.groups')) {
            return;
        }

        $entity = $this->getEntity($event);
        if ($entity === false) {
            return;
        }

        foreach ($this->groupService->fetchGroupTypes() as $groupType) {
            $entity->getLinks()->add(new GroupLink($groupType));
        }
    }

    /**
     * Adds all the org type hal links to the me entity
     *
     * @param Event $event
     */
    public function renderOrgHal(Event $event)
    {
        if (!$this->checkPermission('view.all.orgs')) {
            return;
        }

        $entity = $this->getEntity($event);
        if ($entity === false) {
            return;
        }

        foreach ($this->orgService->fetchOrgTypes() as $orgType) {
            $entity->getLinks()->add(new OrgLink($orgType));
        }
    }

    /**
     * Helps get check the entity
     *
     * @param Event $event
     *
     * @return array|bool|MeEntity
     */
    protected function getEntity(Event $event)
    {
        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return false;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof MeEntity) {
            return false;
        }

        return $realEntity;
    }

    /**
     * Checks the permission
     *
     * @param       $permission
     * @return bool
     */
    protected function checkPermission($permission)
    {
        if (!$this->getAuthenticationService()->hasIdentity()) {
            return false;
        }

        /** @var SecurityUser $authIdentity */
        $authIdentity = $this->getAuthenticationService()->getIdentity();
        return $this->getRbac()->isGranted($authIdentity->getRole(), $permission);
    }
}
