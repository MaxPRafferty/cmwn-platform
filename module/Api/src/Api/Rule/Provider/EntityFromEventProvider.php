<?php

namespace Api\Rule\Provider;

use Rule\Event\Provider\AbstractEventProvider;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class EntityFromEventProvider
 * This fetches the entity from the event
 */
class EntityFromEventProvider extends AbstractEventProvider
{
    const PROVIDER_NAME = "entity";

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
        $entity = $this->getEvent()->getParam('entity');

        if ($entity instanceof Entity) {
            return $entity->getEntity();
        }

        if ($entity instanceof LinkCollectionAwareInterface) {
            return $entity;
        }

        return null;
    }
}
