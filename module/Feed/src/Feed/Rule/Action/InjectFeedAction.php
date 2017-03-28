<?php

namespace Feed\Rule\Action;

use Feed\FeedableInterface;
use Feed\FeedInterface;
use Feed\Rule\Provider\FeedFromFeedableProvider;
use Feed\Service\FeedServiceInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;

/**
 * Saves a feedable item into the feed
 */
class InjectFeedAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $feedableProvider;

    /**
     * @var FeedServiceInterface
     */
    protected $feedService;

    /**
     * @var string
     */
    protected $userParamName;

    /**
     * InjectFeedAction constructor.
     * @param FeedServiceInterface $feedService
     * @param string $userParamName
     * @param string $feedableProvider
     */
    public function __construct(
        FeedServiceInterface $feedService,
        string $userParamName = 'user',
        string $feedableProvider = 'feedable'
    ) {
        $this->feedService = $feedService;
        $this->userParamName = $userParamName;
        $this->feedableProvider = $feedableProvider;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        /** @var FeedableInterface $feedable */
        $feedable = $item->getParam($this->feedableProvider);
        static::checkValueType($feedable, FeedableInterface::class);

        $user = $item->getParam($this->userParamName);

        $feedProvider = new FeedFromFeedableProvider($feedable, $user);
        $feedItem = $feedProvider->getValue();

        static::checkValueType($feedItem, FeedInterface::class);

        $this->feedService->createFeed($feedItem);
    }
}
