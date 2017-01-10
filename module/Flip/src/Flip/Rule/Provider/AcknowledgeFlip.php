<?php

namespace Flip\Rule\Provider;

use Flip\EarnedFlipInterface;
use Flip\Exception\RuntimeException;
use Rule\Provider\ProviderInterface;

/**
 * Provides the latest flip that needs to be acknowledge
 */
class AcknowledgeFlip implements ProviderInterface
{
    const PROVIDER_NAME = 'acknowledge_flip';

    /**
     * @var EarnedFlipInterface
     */
    protected $earnedFlip;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * AcknowledgeFlip constructor.
     *
     * @param EarnedFlipInterface $earnedFlip
     * @param string $providerName
     */
    public function __construct(EarnedFlipInterface $earnedFlip, string $providerName = self::PROVIDER_NAME)
    {
        if ($earnedFlip->isAcknowledged()) {
            throw new RuntimeException(sprintf(
                'Flip "%s" for user "%s" has already been acknowledge',
                $earnedFlip->getFlipId(),
                $earnedFlip->getEarnedBy()
            ));
        }

        $this->earnedFlip   = $earnedFlip;
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
     * @return EarnedFlipInterface
     */
    public function getValue()
    {
        return $this->earnedFlip;
    }
}
