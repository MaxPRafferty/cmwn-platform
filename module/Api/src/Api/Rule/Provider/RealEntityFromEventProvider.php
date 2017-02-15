<?php

namespace Api\Rule\Provider;

/**
 * This fetches the Real entity from the event if the Entity is a HAL Entity
 */
class RealEntityFromEventProvider extends EntityFromEventProvider
{
    const PROVIDER_NAME = "real_entity";

    /**
     * EntityFromEventProvider constructor.
     * @param string $providerName
     */
    public function __construct(string $providerName = self::PROVIDER_NAME)
    {
        parent::__construct($providerName);
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        $value = parent::getValue();
        if ($value == null) {
            return $value;
        }

        return $value->getEntity();
    }
}
