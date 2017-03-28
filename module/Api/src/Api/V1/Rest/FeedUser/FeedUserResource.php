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

    /**
     * Fetches multiple user feed the authenticated user has access too
     *
     * @SWG\Get(path="/user/{user_id}/feed",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="user id of the user requesting feed",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number to fetch",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="per_page",
     *     in="query",
     *     description="Number of user feed returned per page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged user feed",
     *     @SWG\Schema(ref="#/definitions/FeedUserCollection")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  array $params
     *
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = [])
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $result =
            new FeedUserCollection($this->feedUserService->fetchAllFeedForUser($userId, null, new FeedUserEntity([])));
        return $result;
    }

    /**
     * Fetch data for a user feed
     *
     * Fetch the data for a user feed if the requesting user is allowed access.
     *
     * @SWG\Get(path="/user/{user_id}/feed/{feed_id}",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="user id of the user requesting feed",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="feed_id",
     *     in="path",
     *     description="feed id to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="The requested user feed",
     *     @SWG\Schema(ref="#/definitions/FeedUserEntity")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Feed not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $feedId
     *
     * @return ApiProblem|FeedUserEntity
     */
    public function fetch($feedId)
    {
        $userId = $this->getEvent()->getRouteParam('user_id');
        $userFeed = $this->feedUserService->fetchFeedForUser($userId, $feedId);
        return new FeedUserEntity($userFeed->getArrayCopy());
    }

    /**
     * Create a new feed
     *
     * The authenticated user must be allowed to create a new feed in the system
     *
     * @SWG\Post(path="/user/{user_id}/feed/{feed_id}",
     *   tags={"user-feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="user id of the user requesting feed",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="feed_id",
     *     in="path",
     *     description="feed id to fetch",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Feed was attached to user",
     *     @SWG\Schema(ref="#/definitions/GroupEntity")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
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

    /**
     * Update a user feed to set the read flag
     *
     * This enables to set the read flag for a user feed entry
     *
     * @SWG\Put(path="/user/{user_id}/feed/{feed_id}",
     *   tags={"user-feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="user id of the user whose feed needs to be updated",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="feed_id",
     *     in="path",
     *     description="feed Id to update",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Feed data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/UserFeed")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(ref="#/definitions/FeedEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to update a feed",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Feed not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  mixed $feedId
     * @param  mixed $data
     *
     * @return ApiProblem|mixed
     */
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

    /**
     * Delete a User Feed item
     *
     * A fetch is done first to ensure the user has access to a Feed. Then the entry is deleted
     *
     * @SWG\Delete(path="/user/{user_id}/feed/{feed_id}",
     *   tags={"user-feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     name="feed_id",
     *     in="path",
     *     description="feed Id to deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="path",
     *     description="user id of the user whose feed needs to be deleted",
     *     required=true,
     *     type="string",
     *     format="uuid",
     *     maximum=1.0,
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=204,
     *     description="Feed was deleted"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="Feed not found",
     *     @SWG\Schema(ref="#/definitions/NotFoundError")
     *   ),
     *   @SWG\Response(
     *     response=403,
     *     description="Not Authorized to delete or access feed",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   ),
     *   @SWG\Response(
     *     response=401,
     *     description="Not Authenticated",
     *     @SWG\Schema(ref="#/definitions/Error")
     *   )
     * )
     * @param  string $feedId
     *
     * @return ApiProblem|mixed
     */
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
