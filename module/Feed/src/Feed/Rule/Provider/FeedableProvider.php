<?php

namespace Feed\Rule\Provider;

use Feed\Exception\RuntimeException;
use Feed\FeedableInterface;
use Rule\Event\Provider\AbstractEventProvider;

/**
 * Provides a FeedableInterface from then event target
 */
class FeedableProvider extends AbstractEventProvider
{
    /**
     * @var string
     */
    protected $paramName;

    const PROVIDER_NAME = 'feedable_provider';

    /**
     * FeedableProvider constructor.
     * @param string $paramName
     * @param string $providerName
     */
    public function __construct(string $paramName, string $providerName = self::PROVIDER_NAME)
    {
        parent::__construct($providerName);
        $this->paramName = $paramName;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        $feedable = $this->getEvent()->getParam($this->paramName, false);
        if (!$feedable instanceof FeedableInterface) {
            throw new RuntimeException();
        }

        return $feedable;
    }
}
