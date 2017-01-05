<?php

namespace Rule\Engine\Service;

use Rule\Engine\Specification\SpecificationInterface;
use Rule\Utils\AbstractConfigBuilderFactory;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Used to build a specification
 */
class BuildSpecificationFromConfigFactory extends AbstractConfigBuilderFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    protected $itemClassKey = 'specification_class';

    /**
     * @inheritDoc
     */
    protected $instanceOf = SpecificationInterface::class;
}
