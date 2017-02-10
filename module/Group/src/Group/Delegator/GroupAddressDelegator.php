<?php

namespace Group\Delegator;

use Address\AddressInterface;
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
    public function fetchAddressForGroup(GroupInterface $group, AddressInterface $address)
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
}
