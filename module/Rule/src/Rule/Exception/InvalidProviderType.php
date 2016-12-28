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
    public function __construct(string $className, string $provider, string $type)
    {
        parent::__construct(
            sprintf(
                '"%s" requires a provider "%s" that returns a "%s"',
                $className,
                $provider,
                $type
            )
        );
    }
}
