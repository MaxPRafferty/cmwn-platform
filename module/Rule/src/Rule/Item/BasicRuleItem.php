<?php

namespace Rule\Item;

use Rule\Provider\ProviderCollection;
use Rule\Provider\ProviderInterface;
use Zend\EventManager\EventInterface;

/**
 * A Basic Rule Item that takes providers as an array
 */
class BasicRuleItem implements RuleItemInterface
{
    /**
     * @var ProviderCollection
     */
    protected $data;

    /**
     * @inheritDoc
     */
    public function __construct(ProviderInterface ...$providers)
    {
        $this->data = new ProviderCollection(...$providers);
    }

    /**
     * @inheritdoc
     */
    public function getParam(string $param, $default = null)
    {
        return $this->data->offsetExists($param) ? $this->data->offsetGet($param) : $default;
    }

    /**
     * @inheritDoc
     */
    public function setEvent(EventInterface $event): RuleItemInterface
    {
        $this->data->setEvent($event);
        return $this;
    }
}
