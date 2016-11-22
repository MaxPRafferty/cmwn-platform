<?php

namespace Suggest\Filter;

use Group\Group;
use Group\GroupInterface;
use Group\Service\UserGroupServiceInterface;
use Suggest\SuggestionCollection;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;

/**
 * Returns all the users in the same class
 */
class ClassFilter implements FilterCompositeInterface
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var SuggestionCollection
     */
    protected $container;

    /**
     * @var int Number of groups to limit
     */
    protected $groupLimit = 10;

    /**
     * @var int Number of users to limit in each group
     */
    protected $userGroupLimit = 50;

    /**
     * ClassFilter constructor.
     *
     * @param UserGroupServiceInterface $groupService
     * @param int $groupLimit
     * @param int $userGroupLimit
     */
    public function __construct(UserGroupServiceInterface $groupService, $groupLimit = 10, $userGroupLimit = 50)
    {
        $this->groupService   = $groupService;
        $this->groupLimit     = abs($groupLimit);
        $this->userGroupLimit = abs($userGroupLimit);
    }

    /**
     * @param UserInterface $user
     */
    protected function processUser(UserInterface $user)
    {
        $userId = $user->getUserId();
        if (!$this->container->offsetExists($userId)) {
            $this->container->offsetSet($userId, $user);
        }
    }

    /**
     * @param GroupInterface $group
     */
    public function processGroup(GroupInterface $group)
    {
        /** @var GroupInterface $group */
        $users = $this->groupService->fetchUsersForGroup($group)->getItems(0, $this->userGroupLimit);
        array_walk(
            $users,
            [$this, 'processUser']
        );
    }

    /**
     * @inheritdoc
     */
    public function getSuggestions(SuggestionCollection $container, UserInterface $currentUser)
    {
        $this->container = $container;
        // Find all the groups $currentUser Belongs too
        // TODO new service method to fetch by type
        $where = new Where();
        $where->addPredicate(new Operator('g.type', '=', 'class'));
        $groups = $this->groupService
            ->fetchGroupsForUser($currentUser, $where, new Group())
            ->getItems(0, $this->groupLimit);
        array_walk($groups, [$this, 'processGroup']);
    }
}
