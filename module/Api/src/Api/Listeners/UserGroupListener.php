<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\V1\Rest\Group\GroupCollection;
use Api\V1\Rest\Group\GroupEntity;
use Api\V1\Rest\Org\OrgCollection;
use Api\V1\Rest\Org\OrgEntity;
use Api\V1\Rest\User\MeEntity;
use Api\V1\Rest\User\UserEntity;
use Group\Service\UserGroupServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;

/**
 * Class UserGroupListener
 */
class UserGroupListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var bool
     */
    protected $collection = false;

    /**
     * UserGroupListener constructor.
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(UserGroupServiceInterface $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderCollection', [$this, 'flagCollection'], 100);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'attachGroup'], -1000);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'attachOrgs'], -1000);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity', [$this, 'attachHal'], -1000);
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

    public function flagCollection()
    {
        $this->collection = true;
    }

    public function attachHal(Event $event)
    {
        if ($this->collection) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof MeEntity) {
            return;
        }

        foreach ($this->userGroupService->fetchGroupTypesForUser($realEntity) as $type) {
            $link = new GroupLink($type);
            $realEntity->getLinks()->add($link);
        }

        foreach ($this->userGroupService->fetchOrgTypesForUser($realEntity) as $type) {
            $link = new OrgLink($type);
            $realEntity->getLinks()->add($link);
        }
    }

    /**
     * @param Event $event
     */
    public function attachGroup(Event $event)
    {
        if ($this->collection) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof UserEntity) {
            return;
        }

        $payload = $event->getParam('payload');

        /** @var \ZF\Hal\Plugin\Hal $hal */
        $hal     = $event->getTarget();
        $groups  = new GroupCollection($this->userGroupService->fetchGroupsForUser($realEntity, new GroupEntity()));
        $groups->setItemCountPerPage(10);
        $renderedGroups = [];
        /** @var GroupEntity[] $groups */
        foreach ($groups as $group) {
            $entityToRender = new Entity($group->getArrayCopy());
            $renderedGroups[] = $hal->renderEntity($entityToRender);
        }

        $payload['_embedded']['groups'] = $renderedGroups;
    }
    /**
     * @param Event $event
     */
    public function attachOrgs(Event $event)
    {
        if ($this->collection) {
            return;
        }

        $entity  = $event->getParam('entity');
        if (!$entity instanceof Entity) {
            return;
        }

        $realEntity = $entity->entity;

        if (!$realEntity instanceof UserEntity) {
            return;
        }

        $payload = $event->getParam('payload');

        /** @var \ZF\Hal\Plugin\Hal $hal */
        $hal     = $event->getTarget();
        $orgs  = new OrgCollection($this->userGroupService->fetchOrganizationsForUser($realEntity, new OrgEntity()));
        $orgs->setItemCountPerPage(10);
        $renderedGroups = [];
        /** @var OrgEntity[] $orgs */
        foreach ($orgs as $org) {
            $entityToRender   = new Entity($org->getArrayCopy());
            $hal->injectSelfLink($entity, 'api.rest.org', 'org_id');
            $renderedGroups[] = $hal->renderEntity($entityToRender);
        }

        $payload['_embedded']['organizations'] = $renderedGroups;
    }
}
