<?php

namespace Flip\Rule\Rule;

use Application\Exception\NotFoundException;
use Flip\Rule\Provider\AcknowledgeFlip;
use Flip\Service\FlipUserServiceInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Security\Rule\Provider\ActiveUserProvider;
use User\UserInterface;

/**
 * A rule that is satisfied if the active user needs has a flip to acknowledge
 */
class HasAcknowledgeFlip implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var FlipUserServiceInterface
     */
    protected $service;

    /**
     * @var string
     */
    private $providerName;

    /**
     * HasAcknowledgeFlip constructor.
     *
     * @param FlipUserServiceInterface $service
     * @param string $providerName
     */
    public function __construct(
        FlipUserServiceInterface $service,
        string $providerName = ActiveUserProvider::PROVIDER_NAME
    ) {
        $this->service      = $service;
        $this->providerName = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $user = $item->getParam($this->providerName);
        static::checkValueType($user, UserInterface::class);

        try {
            $flip = $this->service->fetchLatestAcknowledgeFlip($user);
            $item->append(new AcknowledgeFlip($flip));
        } catch (NotFoundException $notFound) {
            return false;
        }

        $this->timesSatisfied++;

        return true;
    }
}
