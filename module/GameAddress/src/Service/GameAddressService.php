<?php

namespace GameAddress\Service;

use Game\GameInterface;
use Group\Service\GroupAddressService;
use Group\Service\GroupAddressServiceInterface;
use Group\Service\UserGroupServiceInterface;

class GameAddressService implements GameAddressServiceInterface
{
    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * @var GroupAddressServiceInterface
     */
    protected $groupAddressService;

    /**
     * GameAddressService constructor.
     * @param UserGroupServiceInterface $userGroupService
     * @param GroupAddressServiceInterface $groupAddressService
     */
    public function __construct(
        UserGroupServiceInterface $userGroupService,
        GroupAddressServiceInterface $groupAddressService
    ) {
        $this->userGroupService = $userGroupService;
        $this->ga = $groupAddressService;
    }

    /**
     * @param $users
     * @param GameInterface $game
     */
    public function attachGameToUsers($users, GameInterface $game)
    {
        array_walk($users, function ($user) {
        });
    }

    /**
     * @inheritdoc
     */
    public function attachGameToRegion(GameInterface $game, array $fieldValues = [])
    {
        $groups = $this->groupAddressService->fetchAllGroupsInAddress($fieldValues);
        $groups = $groups->getItems(0, $groups->count());
        array_walk($groups, function ($group) use ($game) {
            $users = $this->userGroupService->fetchUsersForGroup($group);
            $this->attachGameToUsers($users->getItems(0, $users->count()), $game);
        });
        return true;
    }
}
