<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Filter\FilterChain;
use Zend\Filter\StaticFilter;
use Zend\Filter\StringToLower;
use Zend\Filter\Word\CamelCaseToUnderscore;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Creates a table based on the requested name
 */
class AbstractTableFactory implements AbstractFactoryInterface
{
    /**
     * @var string
     */
    protected $pattern = '/table\/*/i';

    /**
     * @var FilterChain
     */
    protected $filter;

    /**
     * AbstractTableFactory constructor.
     */
    public function __construct()
    {
        $this->filter = new FilterChain();
        $this->filter->attach(new CamelCaseToUnderscore())->attach(new StringToLower());
    }

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return preg_match($this->pattern, $requestedName);
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $tableName = preg_replace($this->pattern, '', $requestedName);
        return new TableGateway(
            $this->filter->filter($tableName),
            $container->get(Adapter::class)
        );
    }
}
