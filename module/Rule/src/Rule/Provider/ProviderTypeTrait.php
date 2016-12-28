<?php

namespace Rule\Provider;

use Rule\Exception\InvalidProviderType;

/**
 * Helper trait actions or rules can use to ensure providers return the correct type
 */
trait ProviderTypeTrait
{
    /**
     * Helper trait that ensures the provider returned the correct object type
     *
     * @param $object
     * @param string $type
     *
     * @return bool
     */
    protected static function checkValueType($object, string $type): bool
    {
        if (!$object instanceof $type) {
            throw new InvalidProviderType(
                get_called_class(),
                get_class($object),
                $type
            );
        }

        return true;
    }
}
