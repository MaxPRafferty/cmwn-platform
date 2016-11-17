<?php

namespace Suggest\Service;

use Application\Utils\ServiceTrait;
use Suggest\Engine\SuggestionEngine;
use Suggest\NotFoundException;
use Suggest\Suggestion;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Hydrator\ArraySerializable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class SuggestedFriendService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuggestedService implements SuggestedServiceInterface
{
    use ServiceTrait;
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * FriendService constructor.
     *
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD)
     */
    public function fetchSuggestedFriendForUser($user, $suggestion, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $suggestId = $suggestion instanceof UserInterface ? $suggestion->getUserId() : $suggestion;

        $predicate = new Operator('u.user_id', Operator::OP_EQ, $suggestId);
        $select = $this->createSelect($userId, $predicate);

        $prototype = null === $prototype ? new Suggestion() : $prototype;
        $hydrator = new ArraySerializable();
        /** @var \Iterator|\Countable $results */
        $results  = $this->tableGateway->selectWith($select);

        if (count($results) < 1) {
            throw new NotFoundException();
        }

        $results->rewind();
        $row = $results->current();
        return $hydrator->hydrate($row->getArrayCopy(), $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchSuggestedFriendsForUser($user, $where = null, $prototype = null)
    {
        $userId = ($user instanceof UserInterface)? $user->getUserId() :$user;
        $predicate = new Operator('u.user_id', Operator::OP_NE, $userId);
        $select = $this->createSelect($userId, $predicate);
        $hydrator  = $prototype instanceof UserInterface ? new ArraySerializable() : new UserHydrator();
        $resultSet = new HydratingResultSet($hydrator, $prototype);
        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * adds friend suggestions for a user to the database
     *
     * @inheritdoc
     */
    public function attachSuggestedFriendForUser($user, $suggestion)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $suggestId = $suggestion instanceof UserInterface ? $suggestion->getUserId() : $suggestion;

        try {
            $this->fetchSuggestedFriendForUser($userId, $suggestId, new Suggestion());
        } catch (NotFoundException $nf) {
            $this->tableGateway->insert([
                'user_id' => $userId,
                'suggest_id' => $suggestId
            ]);

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function deleteSuggestionForUser($user, $suggestion)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $suggestId = $suggestion instanceof UserInterface ? $suggestion->getUserId() : $suggestion;

        $where = new Where();
        $firstPredicate = new PredicateSet();
        $firstPredicate->addPredicate(new Operator('user_id', Operator::OP_EQ, $userId));
        $firstPredicate->addPredicate(new Operator('suggest_id', Operator::OP_EQ, $suggestId));
        $secondPredicate = new PredicateSet();
        $secondPredicate->addPredicate(new Operator('suggest_id', Operator::OP_EQ, $userId));
        $secondPredicate->addPredicate(new Operator('user_id', Operator::OP_EQ, $suggestId));
        $where->orPredicate($firstPredicate);
        $where->orPredicate($secondPredicate);

        $this->tableGateway->delete($where);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteAllSuggestionsForUser($user)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $where = new Where();
        $firstPredicate = new PredicateSet();
        $firstPredicate->addPredicate(new Operator('user_id', Operator::OP_EQ, $userId));
        $firstPredicate->addPredicate(new Operator('suggest_id', Operator::OP_EQ, $userId));
        $where->orPredicate($firstPredicate);

        $this->tableGateway->delete($where);
    }

    /**
     * @param String $userId
     * @param PredicateInterface $predicate
     * @return Select
     */
    protected function createSelect($userId, PredicateInterface $predicate)
    {
        $select = new Select(['us' => 'user_suggestions']);

        $select->columns(['status' => new Expression('"CAN_FRIEND"')]);

        $select->join(
            ['u' => 'users'],
            new Expression('u.user_id = us.user_id or u.user_id = us.suggest_id'),
            ['*'],
            Select::JOIN_LEFT
        );
        $predicateSet = new PredicateSet();
        $predicateSet->orPredicate(new Operator('us.user_id', Operator::OP_EQ, $userId));
        $predicateSet->orPredicate(new Operator('us.suggest_id', Operator::OP_EQ, $userId));
        $where = new Where();
        $where->addPredicate($predicateSet);
        $where->addPredicate($predicate);
        $select->where($where);
        return $select;
    }
}
