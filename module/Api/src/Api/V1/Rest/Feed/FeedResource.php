<?php

namespace Api\V1\Rest\Feed;

use Feed\Feed;
use Feed\Service\FeedServiceInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class FeedResource
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
     * @param array $params
     * @return FeedCollection
     */
    public function fetchAll($params = [])
    {
        /**@var \Zend\Paginator\Adapter\DbSelect $feeds*/
        $feeds = $this->feedService->fetchAll(null, new FeedEntity());

        return new FeedCollection($feeds);
    }

    /**
     * @param mixed $feedId
     * @return FeedEntity
     */
    public function fetch($feedId)
    {
        $feed = $this->feedService->fetchFeed($feedId);
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * @param array $data
     * @return FeedEntity
     */
    public function create($data)
    {
        $data = $this->getInputFilter()->getValues();
        $feed = new Feed($data);
        $this->feedService->createFeed($feed);
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * @param mixed $feedId
     * @param mixed $data
     * @return FeedEntity
     */
    public function update($feedId, $data)
    {
        $data = $this->getInputFilter()->getValues();
        $feed = $this->fetch($feedId);
        $this->feedService->updateFeed($feed->exchangeArray($data));
        return new FeedEntity($feed->getArrayCopy());
    }

    /**
     * @param mixed $feedId
     * @return ApiProblem
     */
    public function delete($feedId)
    {
        $feed = $this->fetch($feedId);
        $this->feedService->deleteFeed($feed);
        return new ApiProblem(200, 'feed deleted', 'Ok');
    }
}
