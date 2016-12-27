<?php

namespace Rule\Exception;

/**
 * Throw this exception when an action or rule gets an un-expected type from a provider
 */
class InvalidProviderType extends \RuntimeException
{
    /**
     * @inheritDoc
     */
    public function __construct($className, $type)
    {
        parent::__construct(
            sprintf('"%s" requires a provider that returns a "%s"', $className, $type)
        );
    }
}
