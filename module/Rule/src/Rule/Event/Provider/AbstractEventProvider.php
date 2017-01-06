<?php

namespace Rule\Event\Provider;

use Zend\EventManager\EventInterface;

/**
 * Abstract Event Provider
 */
abstract class AbstractEventProvider implements EventProviderInterface
{
    const PROVIDER_NAME = 'event';

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @var string
     */
    protected $name;

    /**
     * FromEventParamProvider constructor.
     *
     * @param string $providerName
     */
    public function __construct(string $providerName = self::PROVIDER_NAME)
    {
        $this->name = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setEvent(EventInterface $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEvent(): EventInterface
    {
        return $this->event;
    }
}
