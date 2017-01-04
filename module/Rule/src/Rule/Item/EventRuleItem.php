<?php

namespace Rule\Item;

use Rule\Event\Provider\EventProviderInterface;
use Rule\Provider\Collection\ProviderCollection;
use Rule\Provider\Collection\ProviderCollectionAwareInterface;
use Rule\Provider\Collection\ProviderCollectionAwareTrait;
use Rule\Provider\ProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * A Rule item that came from an event
 */
class EventRuleItem implements RuleItemInterface, ProviderCollectionAwareInterface
{
    use ProviderCollectionAwareTrait;

    /**
     * @var EventInterface
     */
    protected $event;

    /**
     * @inheritDoc
     */
    public function __construct(EventInterface $event, ProviderInterface ...$providers)
    {
        $this->event = $event;
        $this->setProviderCollection(new ProviderCollection(...$providers));
    }

    /**
     * @inheritDoc
     */
    public function getParam(string $param, $default = null)
    {
        if (!$this->getProviderCollection()->offsetExists($param)) {
            return $default;
        }

        $provider = $this->getProviderCollection()->getProvider($param);
        if ($provider instanceof EventProviderInterface) {
            $provider->setEvent($this->event);

            return $provider->getValue();
        }

        return $provider->getValue() ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function append(ProviderInterface $provider): RuleItemInterface
    {
        $this->getProviderCollection()->append($provider);
        return $this;
    }
}
