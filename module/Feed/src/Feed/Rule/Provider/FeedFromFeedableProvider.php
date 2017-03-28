<?php

namespace Feed\Rule\Provider;

use Feed\Feed;
use Feed\FeedableInterface;
use Feed\FeedInterface;
use Game\GameInterface;
use Rule\Provider\ProviderInterface;
use Rule\Utils\ProviderTypeTrait;
use User\UserInterface;

/**
 * This provides feed from a feedable
 */
class FeedFromFeedableProvider implements ProviderInterface
{
    use ProviderTypeTrait;

    const PROVIDER_NAME = 'feed_provider';

    /**
     * @var FeedableInterface
     */
    protected $feedable;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * FeedFromFeedableProvider constructor.
     * @param FeedableInterface|null $feedable
     * @param UserInterface $user
     * @param string $providerName
     */
    public function __construct(
        FeedableInterface $feedable = null,
        UserInterface $user = null,
        string $providerName = self::PROVIDER_NAME
    ) {
        $this->feedable = $feedable;
        $this->user = $user;
        $this->providerName = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->providerName;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        static::checkValueType($this->feedable, FeedableInterface::class);

        $meta = $this->feedable->getFeedMeta();

        if ($this->user && !isset($meta['users']['user_id'])) {
            $meta['users']['user_id'] = $this->user->getUserId();
        }

        $feedItem = new Feed([
            'type'          => $this->feedable->getFeedType(),
            'message'       => $this->feedable->getFeedMessage(),
            'title'         => $this->feedable->getFeedTitle(),
            'sender'        => $this->feedable->getFeedSender(),
            'visibility'    => $this->feedable->getFeedVisiblity(),
            'priority'      => $this->feedable->getFeedPriority(),
            'meta'          => $meta,
            'type_version'  => $this->feedable->getFeedTypeVersion(),
        ]);

        return $feedItem;
    }
}
