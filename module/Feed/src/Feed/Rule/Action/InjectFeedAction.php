<?php

namespace Feed\Rule\Action;

use Feed\Feed;
use Feed\FeedableInterface;
use Feed\Service\FeedServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;

/**
 * Saves a feedable item into the feed
 *
 */
class InjectFeedAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var FeedServiceInterface
     */
    protected $feedService;

    /**
     * InjectFeedAction constructor.
     * @param FeedServiceInterface $feedService
     */
    public function __construct(FeedServiceInterface $feedService)
    {
        $this->feedService = $feedService;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        /** @var FeedableInterface $feedable */
        $feedable = $item->getParam('feedable');
        static::checkValueType($feedable, FeedableInterface::class);

        $feedItem = new Feed([
            'type'          => $feedable->getFeedType(),
            'message'       => $feedable->getFeedMessage(),
            'sender'        => $feedable->getSender(),
            'title'         => $feedable->getFeedTitle(),
            'visibility'    => $feedable->getFeedVisiblity(),
            'priority'      => $feedable->getPriority(),
            'meta'          => $feedable->getFeedMeta(),
        ]);

        $this->feedService->createFeed($feedItem);
    }
}
