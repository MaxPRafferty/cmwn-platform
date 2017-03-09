<?php

namespace Group\Delegator;

use Address\AddressInterface;
use Application\Utils\ServiceTrait;
use Group\Service\GroupAddressService;
use Group\Service\GroupAddressServiceInterface;
use Group\GroupInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressDelegator
 * @package Address\Delegator
 */
class GroupAddressDelegator implements GroupAddressServiceInterface
{
    use ServiceTrait;

    /**
     * @var GroupAddressService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * GroupAddressDelegator constructor.
     * @param GroupAddressService $groupAddressService
     * @param EventManagerInterface $eventManager
     */
    public function __construct(GroupAddressService $groupAddressService, EventManagerInterface $eventManager)
    {
        $this->realService = $groupAddressService;
        $this->eventManager = $eventManager;
        $eventManager->addIdentifiers(array_merge(
            [GroupAddressServiceInterface::class, static::class, GroupAddressService::class],
            $eventManager->getIdentifiers()
        ));
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * @inheritdoc
     */
    public function attachAddressToGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $event = new Event(
            'attach.group.address',
            $this->realService,
            [ 'group' => $group, 'address' => $address ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->attachAddressToGroup($group, $address);
            $event->setName('attach.group.address.post');
            $event->setParam('return', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('attach.group.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function detachAddressFromGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $event = new Event(
            'detach.group.address',
            $this->realService,
            [ 'group' => $group, 'address' => $address ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->detachAddressFromGroup($group, $address);
            $event->setName('detach.group.address.post');
            $event->setParam('return', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('detach.group.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAllAddressesForGroup(
        GroupInterface $group,
        $where = null,
        AddressInterface $prototype = null
    ) : AdapterInterface {
        $event = new Event(
            'fetch.all.group.addresses',
            $this->realService,
            [ 'group' => $group, 'where' => $where, 'prototype' => $prototype ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->fetchAllAddressesForGroup($group, $where, $prototype);
            $event->setName('fetch.all.group.addresses.post');
            $event->setParam('addresses', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.all.group.addresses.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAddressForGroup(GroupInterface $group, AddressInterface $address) : AddressInterface
    {
        $event = new Event(
            'fetch.group.address',
            $this->realService,
            [ 'group' => $group, 'address' => $address ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->fetchAddressForGroup($group, $address);
            $event->setName('fetch.group.address.post');
            $event->setParam('address', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.group.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAllGroupsInAddress($where = null, GroupInterface $prototype = null) : AdapterInterface
    {
        $event = new Event(
            'fetch.address.groups',
            $this->realService,
            [ 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->fetchAllGroupsInAddress($where, $prototype);
            $event->setName('fetch.address.groups.post');
            $event->setParam('groups', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.address.groups.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function fetchAddressesWithGroupsAttached(
        $where = null,
        AddressInterface $prototype = null
    ) : AdapterInterface {
        $event = new Event(
            'fetch.all.addresses.with.groups',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->fetchAddressesWithGroupsAttached($where, $prototype);
            $event->setName('fetch.all.addresses.with.groups.post');
            $event->setParam('addresses', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.all.addresses.with.groups.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }
}
