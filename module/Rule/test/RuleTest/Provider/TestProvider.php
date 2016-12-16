<?php

namespace RuleTest\Provider;

use Rule\Provider\ProviderInterface;

/**
 * Class TestProvider
 */
class TestProvider implements ProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'foo';
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return 'bar';
    }

}
