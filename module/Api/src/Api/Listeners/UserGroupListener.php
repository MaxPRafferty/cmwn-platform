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
use Org\Service\OrganizationServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Plugin\Hal;

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
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * UserGroupListener constructor.
     *
     * @param UserGroupServiceInterface    $userGroupService
     * @param OrganizationServiceInterface $orgService
     */
    public function __construct(UserGroupServiceInterface $userGroupService, OrganizationServiceInterface $orgService)
    {
        $this->userGroupService = $userGroupService;
        $this->orgService       = $orgService;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderCollection', [$this, 'flagCollection'], 100);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'attachGroup'], -1000);
        $this->listeners[] = $events->attach('ZF\Hal\Plugin\Hal', 'renderEntity.post', [$this, 'attachOrgs'], -1000);
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
        $orgTypes       = [];
        $groupTypes     = [];
        /** @var OrgEntity[] $orgs */
        foreach ($orgs as $org) {
            $entityToRender   = new Entity($org->getArrayCopy());
            $hal->injectSelfLink($entity, 'api.rest.org', 'org_id');
            $renderedGroups[] = $hal->renderEntity($entityToRender);
            $orgTypes[$org->getType()] = $org->getType();
            $groupTypes = array_merge($groupTypes, $this->orgService->fetchGroupTypes($org));
        }

        $payload['_embedded']['organizations'] = $renderedGroups;

        $this->attachHalLinks($hal, $payload, $orgTypes, $groupTypes);
    }

    protected function attachHalLinks(Hal $hal, \ArrayObject $payload, array $orgTypes, array $groupTypes)
    {
        foreach ($orgTypes as $orgType) {
            $link = new OrgLink($orgType);

            if (array_key_exists($link->getRelation(), $payload['_links'])) {
                continue;
            }

            $payload['_links'][$link->getRelation()] = $hal->getLinkCollectionExtractor()
                ->getLinkExtractor()
                ->extract($link);
        }

        foreach ($groupTypes as $groupType) {
            $link = new GroupLink($groupType);
            
            if (array_key_exists($link->getRelation(), $payload['_links'])) {
                continue;
            }

            $payload['_links'][$link->getRelation()] = $hal->getLinkCollectionExtractor()
                ->getLinkExtractor()
                ->extract($link);
        }
    }
}
