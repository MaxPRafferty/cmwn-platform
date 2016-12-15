<?php

namespace Api\V1\Rest\FeedUser;

use Feed\Service\FeedUserServiceInterface;
use Feed\UserFeed;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FeedUserResource
 */
class FeedUserResource extends AbstractResourceListener
{
    /**
     * @var FeedUserServiceInterface
     */
    protected $feedUserService;

    /**
     * FeedUserResource constructor.
     * @param FeedUserServiceInterface $feedUserService
     */
    public function __construct(FeedUserServiceInterface $feedUserService)
    {
        $this->feedUserService = $feedUserService;
    }

    /**@inheritdoc */
    public function fetchAll($params = [])
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $result =
            new FeedUserCollection($this->feedUserService->fetchAllFeedForUser($userId, null, new FeedUserEntity([])));
        return $result;
    }

    /**@inheritdoc */
    public function fetch($feedId)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $userFeed = $this->feedUserService->fetchFeedForUser($userId, $feedId);
        return new FeedUserEntity($userFeed->getArrayCopy());
    }

    /**@Inheritdoc */
    public function create($data)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $feedId = $this->getEvent()->getRouteParam('feed_id');

        $data = $this->getInputFilter()->getValues();
        $userFeed = new UserFeed($data);
        $userFeed->setFeedId($feedId);

        $this->feedUserService->attachFeedForUser($userId, $userFeed);
        return new FeedUserEntity($userFeed->getArrayCopy());
    }

    /**@Inheritdoc*/
    public function update($feedId, $data)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $feedId = $this->getEvent()->getRouteParam('feed_id');

        $data = $this->getInputFilter()->getValues();
        $userFeed = new UserFeed($data);
        $userFeed->setFeedId($feedId);

        $this->feedUserService->updateFeedForUser($userId, $userFeed);
        return new FeedUserEntity($userFeed->getArrayCopy());
    }

    /**@Inheritdoc*/
    public function delete($feedId)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $feedId = $this->getEvent()->getRouteParam('feed_id');

        $userFeed = new UserFeed([]);
        $userFeed->setFeedId($feedId);

        $this->feedUserService->deleteFeedForUser($userId, $userFeed);
        return new ApiProblem(200, 'User feed deleted', 'Ok');
    }
}
