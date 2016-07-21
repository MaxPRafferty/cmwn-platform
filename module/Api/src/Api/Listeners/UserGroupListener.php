<?php

namespace Api\Listeners;

use Api\Links\GroupLink;
use Api\Links\OrgLink;
use Api\V1\Rest\Group\GroupCollection;
use Api\V1\Rest\Group\GroupEntity;
use Api\V1\Rest\Org\OrgCollection;
use Api\V1\Rest\Org\OrgEntity;
use Api\V1\Rest\User\UserEntity;
use Group\Service\UserGroupServiceInterface;
use Org\Service\OrganizationServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;
use ZF\Hal\Entity;
use ZF\Hal\Plugin\Hal;

/**
 * Class UserGroupListener
 *
 * @todo Breakup into smaller classes
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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

    /**
     * Flags that we are rendering a collection
     *
     * Attach group skips when we are rendering a collection of users
     */
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
        $groups  = new GroupCollection($this->userGroupService->fetchGroupsForUser(
            $realEntity,
            null,
            new GroupEntity()
        ));
        $groups->setItemCountPerPage(10);
        $renderedGroups = [];
        $groupTypes     = [];
        /** @var GroupEntity[] $groups */
        foreach ($groups as $group) {
            $entityToRender = new Entity($group);
            $renderedGroups[] = $hal->renderEntity($entityToRender);
            array_push($groupTypes, $group->getType());
        }

        $payload['_embedded']['groups'] = $renderedGroups;
        $this->attachGroupHalLinks($hal, $payload, $groupTypes);
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

        // CORE-786 children no longer get access to districts
        if ($realEntity->getType() === UserInterface::TYPE_CHILD) {
            return;
        }

        $payload = $event->getParam('payload');

        /** @var \ZF\Hal\Plugin\Hal $hal */
        $hal     = $event->getTarget();
        $orgs  = new OrgCollection($this->userGroupService->fetchOrganizationsForUser($realEntity, new OrgEntity()));
        $orgs->setItemCountPerPage(10);
        $renderedGroups = [];
        $orgTypes       = [];
        /** @var OrgEntity[] $orgs */
        foreach ($orgs as $org) {
            $entityToRender   = new Entity($org->getArrayCopy());
            $hal->injectSelfLink($entity, 'api.rest.org', 'org_id');
            $renderedGroups[] = $hal->renderEntity($entityToRender);
            $orgTypes[$org->getType()] = $org->getType();
        }

        $payload['_embedded']['organizations'] = $renderedGroups;

        $this->attachOrgHalLinks($hal, $payload, $orgTypes);
    }

    /**
     * Attaches the Org Hal Links
     *
     * @param Hal $hal
     * @param \ArrayObject $payload
     * @param array $orgTypes
     */
    protected function attachOrgHalLinks(Hal $hal, \ArrayObject $payload, array $orgTypes)
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
    }

    /**
     * @param Hal $hal
     * @param \ArrayObject $payload
     * @param array $groupTypes
     */
    protected function attachGroupHalLinks(Hal $hal, \ArrayObject $payload, array $groupTypes)
    {
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
