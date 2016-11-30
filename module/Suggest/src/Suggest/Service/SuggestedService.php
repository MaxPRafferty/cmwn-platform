<?php

namespace Suggest\Service;

use Application\Utils\ServiceTrait;
use Suggest\NotFoundException;
use Suggest\Suggestion;
use User\Utils\ExtractUserIdTrait;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Hydrator\ArraySerializable;
use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class SuggestedFriendService
 */
class SuggestedService implements SuggestedServiceInterface
{
    use ServiceTrait;
    use ExtractUserIdTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

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
     */
    public function fetchSuggestedFriendForUser($user, $suggestion, $prototype = null)
    {
        $userId    = $this->extractUserId($user);
        $suggestId = $this->extractUserId($suggestion);

        $predicate = new Operator('u.user_id', '=', $suggestId);
        $select    = $this->createSelect($userId, $predicate);

        $hydrator = new ArraySerializable();
        $results  = $this->tableGateway->selectWith($select);

        if (count($results) < 1) {
            throw new NotFoundException();
        }

        $results->rewind();

        return $hydrator->hydrate($results->current()->getArrayCopy(), $this->createPrototype($prototype));
    }

    /**
     * @inheritdoc
     */
    public function fetchSuggestedFriendsForUser($user, $where = null, $prototype = null)
    {
        $userId    = $this->extractUserId($user);
        $predicate = new Operator('u.user_id', '!=', $userId);
        $select    = $this->createSelect($userId, $predicate);

        $hydrator  = new ArraySerializable();
        $resultSet = new HydratingResultSet($hydrator, $this->createPrototype($prototype));

        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function attachSuggestedFriendForUser($user, $suggestion)
    {
        $userId    = $this->extractUserId($user);
        $suggestId = $this->extractUserId($suggestion);

        try {
            $this->fetchSuggestedFriendForUser($userId, $suggestId, new Suggestion());
        } catch (NotFoundException $nf) {
            $this->tableGateway->insert([
                'user_id'    => $userId,
                'suggest_id' => $suggestId,
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
        $userId    = $this->extractUserId($user);
        $suggestId = $this->extractUserId($suggestion);
        $where     = $this->createWhere([]);

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
        $userId          = $this->extractUserId($user);
        $where           = $this->createWhere([]);
        $deletePredicate = new PredicateSet();
        $deletePredicate->orPredicate(new Operator('user_id', '=', $userId));
        $deletePredicate->orPredicate(new Operator('suggest_id', '=', $userId));
        $where->addPredicate($deletePredicate);

        $this->tableGateway->delete($where);
    }

    /**
     * @param String $userId
     * @param PredicateInterface $predicate
     *
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

        $where = $this->createWhere([]);
        $where->addPredicate($predicateSet);
        $where->addPredicate($predicate);
        $select->where($where);

        return $select;
    }

    /**
     * @param $prototype
     *
     * @return Suggestion
     */
    protected function createPrototype($prototype)
    {
        return null === $prototype ? new Suggestion() : $prototype;
    }
}
