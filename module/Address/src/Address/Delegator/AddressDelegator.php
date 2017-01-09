<?php

namespace Address\Delegator;

use Address\AddressInterface;
use Address\Service\AddressService;
use Address\Service\AddressServiceInterface;
use Application\Exception\NotFoundException;
use Zend\EventManager\Event;
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
     * @inheritdoc
     */
    public function fetchAddress(string $addressId, $prototype = null) : AddressInterface
    {

    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, $prototype = null) : DbSelect
    {
        $event = new Event(
            'fetch.all.addresses',
            $this->realService,
            [ 'where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $prototype);
        $event->setName('fetch.all.flagged.images.post');
        $event->setParam('flagged-images', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function createAddress(AddressInterface $address) : bool
    {
    }

    /**
     * @inheritdoc
     */
    public function updateAddress(AddressInterface $address) : bool
    {
    }

    /**
     * @inheritdoc
     */
    public function deleteAddress(AddressInterface $address) : bool
    {
    }
}
