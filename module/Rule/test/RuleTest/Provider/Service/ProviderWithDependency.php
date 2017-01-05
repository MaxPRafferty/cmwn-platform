<?php

namespace RuleTest\Provider\Service;

use Rule\Provider\BasicValueProvider;

/**
 * Class ProviderWithDependency
 */
class ProviderWithDependency extends BasicValueProvider
{
    /**
     * @var ProviderDependency
     */
    public $depend;

    /**
     * ProviderWithDependency constructor.
     *
     * @param ProviderDependency $dependency
     */
    public function __construct(ProviderDependency $dependency)
    {
        $this->depend = $dependency;
        parent::__construct('foo', 'bar');
    }
}
