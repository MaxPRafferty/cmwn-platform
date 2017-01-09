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
    protected $eventParam;

    /**
     * FromEventParamProvider constructor.
     *
     * @param string $providerName
     * @param null $eventParam
     */
    public function __construct($providerName, $eventParam = null)
    {
        parent::__construct($providerName);
        $this->eventParam = $eventParam === null ? $providerName : $eventParam;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->getEvent()->getParam($this->eventParam, null);
    }
}
