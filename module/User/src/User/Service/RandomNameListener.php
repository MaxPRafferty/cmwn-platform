<?php

namespace User\Service;

use User\Child;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

/**
 * Class RandomNameListener
 *
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
     *
     * @param TableGateway $table
     */
    public function __construct(TableGateway $table)
    {
        $this->gateway = $table;
    }

    /**
     * @param SharedEventManagerInterface $manager
     *
     * @codeCoverageIgnore
     */
    public function attachShared(SharedEventManagerInterface $manager)
    {
        $this->listeners[] = $manager->attach(
            UserServiceInterface::class,
            'save.new.user',
            [$this, 'reserveRandomName']
        );

        $this->listeners[] = $manager->attach(
            UserServiceInterface::class,
            'save.user',
            [$this, 'reserveRandomName']
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     *
     * @codeCoverageIgnore
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $listener) {
            $manager->detach(UserServiceInterface::class, $listener);
        }
    }

    /**
     * @param Event $event
     *
     * @return null
     */
    public function reserveRandomName(Event $event)
    {
        $child = $event->getParam('user', null);
        if (!$child instanceof Child) {
            return null;
        }

        // This SHOULD generate a name if no name is set
        // FIXME maybe remove the conditional
        $child->getUserName();
        if (!$child->isNameGenerated()) {
            return null;
        }

        $userName = $child->getGeneratedName();
        $results  = $this->gateway->select(['name' => [$userName->left, $userName->right]]);

        if ($results->count() < 2) {
            return null;
        }

        $wordValues = [
            'LEFT'  => 1,
            'RIGHT' => 1,
        ];

        foreach ($results as $word) {
            $wordValues[$word['position']] = $word['count'];
        }

        $userName->setValues($wordValues['LEFT'], $wordValues['RIGHT']);
        $this->gateway->update(
            ['count' => new Expression('count + 1')],
            ['name' => [$userName->left, $userName->right]]
        );

        return null;
    }
}
