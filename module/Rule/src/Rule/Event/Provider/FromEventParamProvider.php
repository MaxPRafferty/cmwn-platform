<?php

namespace Rule\Event\Provider;

/**
 * This Provides data from an event Parameter
 */
class FromEventParamProvider extends AbstractEventProvider
{
    /**
     * @var string
     */
    protected $eventName;

    public function __construct($providerName, $eventParam = null)
    {
        parent::__construct($providerName);
        $this->eventName = $eventParam === null ? $providerName : $eventParam;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getEvent()->getParam($this->eventName, null);
    }
}
