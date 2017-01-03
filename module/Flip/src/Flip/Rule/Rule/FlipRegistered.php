<?php

namespace Flip\Rule\Rule;

use Application\Exception\NotFoundException;
use Flip\Service\FlipServiceInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * A Rule that is happy when a flip is registered in the DB
 */
class FlipRegistered implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var FlipServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    protected $flipId;

    /**;
     * FlipRegistered constructor.
     *
     * @param FlipServiceInterface $service
     * @param string $flipId
     */
    public function __construct(FlipServiceInterface $service, string $flipId)
    {
        $this->service = $service;
        $this->flipId = $flipId;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        try {
            $this->service->fetchFlipById($this->flipId);
        } catch (NotFoundException $flipNotFound) {
            return false;
        }

        $this->timesSatisfied++;
        return true;
    }
}
