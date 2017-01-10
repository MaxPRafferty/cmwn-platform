<?php

namespace Address\Delegator;

use Address\AddressInterface;
use Address\Service\GroupAddressService;
use Address\Service\GroupAddressServiceInterface;
use Group\GroupInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GroupAddressDelegator
 * @package Address\Delegator
 */
class GroupAddressDelegator implements GroupAddressServiceInterface
{
    use EventManagerAwareTrait;

    /**
     * @var GroupAddressService
     */
    protected $realService;

    /**
     * GroupAddressDelegator constructor.
     * @param GroupAddressService $groupAddressService
     * @param EventManagerInterface $eventManager
     */
    public function __construct(GroupAddressService $groupAddressService, EventManagerInterface $eventManager)
    {
        $this->realService = $groupAddressService;
        $this->setEventManager($eventManager);
    }

    /**
     * @inheritdoc
     */
    public function attachAddressToGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $event = new Event(
            'attach.address',
            $this->realService,
            [ 'group' => $group, 'address' => $address ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->attachAddressToGroup($group, $address);
        $event->setName('attach.address.post');
        $event->setParam('return', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function detachAddressFromGroup(GroupInterface $group, AddressInterface $address) : bool
    {
        $event = new Event(
            'detach.address',
            $this->realService,
            [ 'group' => $group, 'address' => $address ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->detachAddressFromGroup($group, $address);
        $event->setName('detach.address.post');
        $event->setParam('return', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAllAddressesForGroup(GroupInterface $group, $where = null, $prototype = null) : DbSelect
    {
        $event = new Event(
            'fetch.all.addresses.group',
            $this->realService,
            [ 'group' => $group, 'where' => $where, 'prototype' => $prototype ]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAllAddressesForGroup($group, $where, $prototype);
        $event->setName('fetch.all.addresses.group.post');
        $event->setParam('addresses', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
