<?php

namespace Api\V1\Rest\Feed;

use Feed\Feed;
use Feed\Service\FeedServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Resource dealing with feed
 */
class FeedResource extends AbstractResourceListener
{
    /**
     * @var FeedServiceInterface
     */
    protected $feedService;

    /**
     * FeedResource constructor.
     * @param FeedServiceInterface $feedService
     */
    public function __construct(FeedServiceInterface $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * Fetches multiple feed the authenticated user has access too
     *
     * @SWG\Get(path="/feed",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
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
     *     description="Number of feed returned per page",
     *     type="integer",
     *     format="int32",
     *     maximum=1.0
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Paged feed",
     *     @SWG\Schema(ref="#/definitions/FeedCollection")
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
        /**@var \Zend\Paginator\Adapter\DbSelect $feeds*/
        $feeds = $this->feedService->fetchAll(null, new FeedEntity());

        return new FeedCollection($feeds);
    }

    /**
     * Fetch data for a feed
     *
     * Fetch the data for a feed if the authenticated user is allowed access.
     *
     * @SWG\Get(path="/feed/{feed_id}",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
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
     *     description="The requested feed",
     *     @SWG\Schema(ref="#/definitions/FeedEntity")
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
     * @return ApiProblem|FeedEntity
     */
    public function fetch($feedId)
    {
        $feed = $this->feedService->fetchFeed($feedId);
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * Create a new feed
     *
     * The authenticated user must be allowed to create a new feed in the system
     *
     * @SWG\Post(path="/feed",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     description="Feed data",
     *     required=true,
     *     @SWG\Schema(ref="#/definitions/Feed")
     *   ),
     *   @SWG\Response(
     *     response=201,
     *     description="Feed was created",
     *     @SWG\Schema(ref="#/definitions/GroupEntity")
     *   ),
     *   @SWG\Response(
     *     response=422,
     *     description="Validation failed",
     *     @SWG\Schema(ref="#/definitions/ValidationError")
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
        $data = $this->getInputFilter()->getValues();
        $feed = new Feed($data);
        $this->feedService->createFeed($feed);
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * Update a feed
     *
     * The user must be allowed access to the feed and be allowed to edit feed.  403 is returned if the user is not
     * allowed access to update the feed. 404 is returned if the feed is not found or the user is not allowed access
     *
     * @SWG\Put(path="/feed/{feed_id}",
     *   tags={"feed"},
     *   @SWG\SecurityScheme(
     *     type="basic",
     *     description="HTTP Basic auth",
     *     securityDefinition="basic"
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
     *     @SWG\Schema(ref="#/definitions/Feed")
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
        $data = $this->getInputFilter()->getValues();
        $feed = $this->fetch($feedId);
        $this->feedService->updateFeed($feed->exchangeArray($data));
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * Delete a Feed item
     *
     * A fetch is done first to ensure the user has access to a Feed.  By default feeds are soft deleted.
     * The authenticated user will get a 403 if the they are not allowed to delete a feed
     *
     * @SWG\Delete(path="/feed/{feed_id}",
     *   tags={"feed"},
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
        $feed = $this->fetch($feedId);
        $this->feedService->deleteFeed($feed);
        return new ApiProblem(200, 'feed deleted', 'Ok');
    }
}
