<?php

namespace Api\V1\Rest\Friend;

use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use User\UserInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FriendResource
 */
class FriendResource extends AbstractResourceListener
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * FriendResource constructor.
     *
     * @param FriendServiceInterface $friendService
     */
    public function __construct(FriendServiceInterface $friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * Gets the user from the route
     *
     * @return UserInterface
     */
    protected function getUser()
    {
        return $this->getEvent()->getRouteParam('user', null);
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $friendId = $this->getInputFilter()->getValue('friend_id');
        $this->friendService->attachFriendToUser($this->getUser(), $friendId);
        return $this->fetch($friendId);
    }

    /**
     * Delete a resource
     *
     * @param  mixed $friendId
     * @return ApiProblem|mixed
     */
    public function delete($friendId)
    {
        $friend = $this->fetch($friendId);
        if (!$friend instanceof FriendEntity) {
            return $friend;
        }

        $this->friendService->detachFriendFromUser($this->getUser(), $friend);
        return true;
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $friendId
     * @return ApiProblem|FriendEntity|UserInterface
     */
    public function fetch($friendId)
    {
        try {
            return $this->friendService->fetchFriendForUser($this->getUser(), $friendId, new FriendEntity());
        } catch (NotFriendsException $notFriends) {
            return new ApiProblem($notFriends->getCode(), $notFriends->getMessage());
        }
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        return new FriendCollection(
            $this->friendService->fetchFriendsForUser($this->getUser(), null, new FriendEntity())
        );
    }
}
