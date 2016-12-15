<?php

namespace Rule;

/**
 * Class RuleItem
 */
class RuleItem implements RuleItemInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @inheritDoc
     */
    public function __construct(array $data = [])
    {
        $this->exchangeArray($data);
    }

    /**
     * @inheritdoc
     */
    public function getParam(string $param, $default = null)
    {
        $value = $this->data[$param] ?? $default;

        return is_object($value) ? clone $value : $value;
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function exchangeArray(array $data)
    {
        $this->data = $data;
    }
}
