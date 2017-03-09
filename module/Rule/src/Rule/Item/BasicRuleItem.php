<?php

namespace Rule\Item;

use Rule\Provider\Collection\ProviderCollection;
use Rule\Provider\ProviderInterface;

/**
 * A Basic Rule Item that takes providers as an array
 */
class BasicRuleItem implements RuleItemInterface
{
    use RuleItemTrait;

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
    public function append(ProviderInterface $provider): RuleItemInterface
    {
        $this->data->append($provider);
        return $this;
    }
}
