<?php

namespace Suggest\Filter;

use Group\Group;
use Group\GroupInterface;
use Group\Service\UserGroupServiceInterface;
use Suggest\Suggestion;
use Suggest\SuggestionContainer;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;

/**
 * Class ClassRule
 */
class ClassFilter implements SuggestedFilterCompositeInterface
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $groupService;

    /**
     * @var SuggestionContainer
     */
    protected $container;

    /**
     * ClassRule constructor.
     * @param UserGroupServiceInterface $groupService
     */
    public function __construct(UserGroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * @param UserInterface array $users
     */
    protected function processUsers($users)
    {
        /**@var UserInterface $user*/
        foreach ($users as $user) {
            $suggestion = new Suggestion($user->getArrayCopy());
            $this->container[$user->getUserId()] = $suggestion;
        }
    }

    /**
     * @param $groups
     */
    protected function processGroups($groups)
    {
        /** @var GroupInterface $group */
        foreach ($groups as $group) {
            $users = $this->groupService->fetchUsersForGroup($group);
            $users = $users->getItems(0, 50);
            $this->processUsers($users);
        }
    }

    /**
     * @param $currentUser
     */
    protected function buildSuggestions($currentUser)
    {
        $this->container = new SuggestionContainer();
        $where = new Where();
        $where->addPredicate(new Operator('g.type', Operator::OP_EQ, 'class'));
        $groups = $this->groupService->fetchGroupsForUser($currentUser, $where, new Group());

        $groups = $groups->getItems(0, 10);
        if ($groups === null) {
            return;
        }
        $this->processGroups($groups);
    }

    /**
     * @inheritdoc
     */
    public function getSuggestions($currentUser)
    {
        $this->buildSuggestions($currentUser);
        return $this->container;
    }
}
