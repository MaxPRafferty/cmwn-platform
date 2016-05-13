<?php

namespace Friend\Service;

use Application\Utils\ServiceTrait;
use Group\Service\UserGroupServiceInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Predicate\Operator;

/**
 * Class SuggestedFriendService
 */
class SuggestedFriendService implements SuggestedFriendServiceInterface
{
    use ServiceTrait;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * SuggestedFriendService constructor.
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(UserGroupServiceInterface $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * Fetches the suggested users for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null)
    {
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('u.type', '=', UserInterface::TYPE_CHILD));
        $where->addPredicate(new IsNull('uf.status'));

        return $this->userGroupService->fetchAllUsersForUser($user, $where, $prototype);
    }
}
