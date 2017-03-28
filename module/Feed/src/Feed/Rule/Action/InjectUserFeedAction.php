<?php

namespace Feed\Rule\Action;

use Feed\FeedInterface;
use Feed\Service\FeedUserServiceInterface;
use Feed\UserFeed;
use Rule\Action\ActionInterface;
use Rule\Event\Provider\FromEventParamProvider;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;

/**
 * Action to attach feed to a user
 */
class InjectUserFeedAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var FeedUserServiceInterface
     */
    protected $feedUseService;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * InjectUserFeedAction constructor.
     * @param FeedUserServiceInterface $feedUserService
     * @param string $providerName
     */
    public function __construct(
        FeedUserServiceInterface $feedUserService,
        string $providerName = FromEventParamProvider::PROVIDER_NAME
    ) {
        $this->providerName = $providerName;
        $this->feedUseService = $feedUserService;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $feed = $item->getParam($this->providerName);
        static::checkValueType($feed, FeedInterface::class);

        $meta = $feed->getMeta();

        $users = $meta['users'] ?? null;

        if (!is_array($users)) {
            return;
        }

        foreach ($users as $offset => $user) {
            $this->feedUseService->attachFeedForUser($user, new UserFeed($feed->getArrayCopy()));
        };
    }
}
