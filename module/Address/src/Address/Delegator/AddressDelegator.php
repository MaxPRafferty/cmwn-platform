<?php

namespace Address\Delegator;

use Address\AddressInterface;
use Address\Service\AddressService;
use Address\Service\AddressServiceInterface;
use Application\Exception\NotFoundException;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class AddressDelegator
 * @package Address\Delegator
 */
class AddressDelegator implements AddressServiceInterface
{
    /**
     * @var AddressService
     */
    protected $realService;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * AddressDelegator constructor.
     * @param AddressService $addressService
     * @param EventManagerInterface $eventManager
     */
    public function __construct(AddressService $addressService, EventManagerInterface $eventManager)
    {
        $this->realService = $addressService;
        $this->eventManager = $eventManager;
        $eventManager->addIdentifiers(array_merge(
            [AddressServiceInterface::class, static::class, AddressService::class],
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
    public function fetchAddress(string $addressId, AddressInterface $prototype = null) : AddressInterface
    {
        $event = new Event(
            'fetch.address',
            $this->realService,
            ['address_id' => $addressId, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->fetchAddress($addressId, $prototype);
            $event->setName('fetch.address.post');
            $event->setParam('address', $return);
            $this->getEventManager()->triggerEvent($event);
            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, AddressInterface $prototype = null) : DbSelect
    {
        $event = new Event(
            'fetch.all.addresses',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->fetchAll($where, $prototype);
            $event->setName('fetch.all.addresses.post');
            $event->setParam('addresses', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('fetch.all.addresses.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function createAddress(AddressInterface $address) : bool
    {
        $event = new Event(
            'create.address',
            $this->realService,
            ['address' => $address]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->createAddress($address);
            $event->setName('create.address.post');
            $event->setParam('return', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('create.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function updateAddress(AddressInterface $address) : bool
    {
        $event = new Event(
            'update.address',
            $this->realService,
            ['address' => $address]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }
        try {
            $return = $this->realService->updateAddress($address);
            $event->setName('update.address.post');
            $event->setParam('return', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('update.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteAddress(AddressInterface $address) : bool
    {
        $event = new Event(
            'delete.address',
            $this->realService,
            ['address' => $address]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->deleteAddress($address);
            $event->setName('delete.address.post');
            $event->setParam('return', $return);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $e) {
            $event->setName('delete.address.error');
            $event->setParam('exception', $e);
            $this->getEventManager()->triggerEvent($event);
            throw $e;
        }
    }
}
