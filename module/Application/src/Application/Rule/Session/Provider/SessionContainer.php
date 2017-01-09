<?php

namespace Application\Rule\Session\Provider;

use Rule\Provider\ProviderInterface;
use Zend\Session\Container;

/**
 * Provides a session container
 */
class SessionContainer implements ProviderInterface
{
    const PROVIDER_NAME = 'CmwnContainer';

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var string
     */
    protected $name;

    /**
     * SessionContainer constructor.
     *
     * @param Container $container
     * @param string $name
     */
    public function __construct(Container $container, string $name = 'CmwnContainer')
    {
        $this->container = $container;
        $this->name      = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     * @return Container
     */
    public function getValue()
    {
        return $this->container;
    }
}
