<?php

namespace User\Service;

use User\Child;
use User\Delegator\UserServiceDelegator;
use User\UserInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class RandomNameListener
 * @package User\Service
 */
class RandomNameListener
{
    /**
     * @var TableGateway
     */
    protected $gateway;

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = [];

    /**
     * RandomNameListener constructor.
     * @param TableGateway $table
     */
    public function __construct(TableGateway $table)
    {
        $this->gateway = $table;
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        $this->listeners[] = $manager->attach(
            UserServiceDelegator::class,
            'save.new.user',
            [$this, 'reserveRandomName']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(UserServiceDelegator::class, $listener);
        }
    }

    /**
     * @param Event $event
     */
    public function reserveRandomName(Event $event)
    {
        $child = $event->getParam('user', null);
        if (!$child instanceof Child) {
            return;
        }

        $child->getUserName();
        $userName = $child->getGeneratedName();

        $results = $this->gateway->select(['name' => [$userName->left, $userName->right]]);

        if ($results->count() < 2) {
            // runtime exception?
            return;
        }

        $wordCounts = [
            'left'  => 1,
            'right' => 2,
        ];
        foreach ($results as $word) {
            
        }
    }
}
