<?php

namespace User\Delegator;

use Application\Exception\DuplicateEntryException;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class CheckUserListener
 * @package User\Delegator
 */
class CheckUserListener implements ListenerAggregateInterface
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
        $this->listeners[] = $events->attach('save.user', [$this, 'checkUniqueFields']);
    }

    /**
     * Checks if the user name or email already exists
     *
     * @param Event $event
     * @throws DuplicateEntryException
     */
    public function checkUniqueFields(Event $event)
    {
        $userService = $event->getTarget();
        if (!$userService instanceof UserServiceInterface) {
            return;
        }

        $user        = $event->getParam('user');
        if (!$user instanceof UserInterface) {
            return;
        }

        // SELECT `users`.* FROM `users` WHERE (`email` = :email OR `username` = :username) AND `user_id` != :user_id

        $predicate = new PredicateSet([
            new PredicateSet([
                new Operator('email', Operator::OP_EQ, $user->getEmail()),
                new Operator('username', Operator::OP_EQ, $user->getUserName())
            ], PredicateSet::OP_OR),

            new Operator('user_id', Operator::OP_NE, $user->getUserId())
        ]);


        $results = $userService->fetchAll($predicate, false);

        if (count($results) > 0) {
            $event->stopPropagation(true);
            throw new DuplicateEntryException('Invalid user');
        }
    }
}
