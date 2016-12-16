<?php

namespace Rule\Item;

use Rule\Provider\ProviderCollection;
use Rule\Provider\ProviderInterface;

/**
 * Class RuleItem
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
}
