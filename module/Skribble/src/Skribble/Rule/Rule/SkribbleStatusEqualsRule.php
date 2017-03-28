<?php

namespace Skribble\Rule\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Skribble\SkribbleInterface;

/**
 * Compares skribble status to provided status
 */
class SkribbleStatusEqualsRule implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $skribbleStatus;

    /**
     * @var string
     */
    protected $skribbleProvider;

    /**
     * SkribbleStatusEqualsRule constructor.
     * @param string $skribbleStatus
     * @param string $providerName
     */
    public function __construct(
        string $providerName = 'skribble',
        string $skribbleStatus = SkribbleInterface::STATUS_COMPLETE
    ) {
        $this->skribbleStatus = $skribbleStatus;
        $this->skribbleProvider = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $skribble = $item->getParam($this->skribbleProvider);
        static::checkValueType($skribble, SkribbleInterface::class);

        if ($skribble->getStatus() === $this->skribbleStatus) {
            return true;
        }

        return false;
    }
}
