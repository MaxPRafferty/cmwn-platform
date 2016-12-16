<?php

namespace Rule\Item;

use Rule\Provider\ProviderInterface;

/**
 * Class RuleItem
 */
class BasicRuleItem implements RuleItemInterface
{
    /**
     * @var \ArrayObject
     */
    protected $data;

    /**
     * @inheritDoc
     */
    public function __construct(ProviderInterface ...$providers)
    {
        $this->data = new \ArrayObject();
        if ($providers === null) {
            return;
        }
        array_walk($providers, function (ProviderInterface $provider) {
            $this->data->offsetSet($provider->getName(), $provider->getValue());
        });
    }

    /**
     * @inheritdoc
     */
    public function getParam(string $param, $default = null)
    {
        $value = $this->data->offsetExists($param) ? $this->data->offsetGet($param) : $default;
        return is_object($value) ? clone $value : $value;
    }
}
