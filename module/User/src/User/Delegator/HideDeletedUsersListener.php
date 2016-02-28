<?php

namespace User\Delegator;

use Application\Exception\NotFoundException;
use User\UserInterface;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class HideDeletedUsersListener
 *
 * @todo Allow some users to be able to see deleted users
 * @todo Make this class more genric
 * @package User\Delegator
 */
class HideDeletedUsersListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch.all.users', [$this, 'hideUsers']);
        $this->listeners[] = $events->attach('fetch.user.post', [$this, 'hideUser']);
    }

    public function hideUsers(Event $event)
    {
        $where = $event->getParam('where');
        if (!$where instanceof Where) {
            return;
        }

        $where->addPredicate(new IsNull('deleted'));
    }

    public function hideUser(Event $event)
    {
        $user = $event->getParam('user');
        if (!$user instanceof UserInterface) {
            return;
        }

        if ($user->isDeleted()) {
            throw new NotFoundException('User not found');
        }
    }
}
