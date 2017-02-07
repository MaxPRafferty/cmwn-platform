<?php

namespace Application\Rule\Session\Provider;

use Rule\Provider\ProviderInterface;
use Zend\Session\Container;

/**
 * Provides data from the session
 */
class SessionValue implements ProviderInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $containerParam;

    /**
     * @var string
     */
    private $providerName;

    /**
     * SessionValue constructor.
     *
     * @param Container $container
     * @param string $containerParam
     * @param string|null $providerName
     */
    public function __construct(Container $container, string $containerParam, string $providerName = null)
    {
        $this->container      = $container;
        $this->containerParam = $containerParam;
        $this->providerName   = $providerName ?? $containerParam;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->providerName;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->container->offsetGet($this->containerParam);
    }
}
