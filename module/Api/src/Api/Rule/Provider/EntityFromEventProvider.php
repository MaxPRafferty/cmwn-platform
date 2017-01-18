<?php


namespace Api\Rule\Provider;

use Rule\Event\Provider\AbstractEventProvider;
use Rule\Event\Provider\EventProviderInterface;
use Rule\Provider\ProviderInterface;
use ZF\Hal\Entity;
use ZF\Hal\Link\LinkCollectionAwareInterface;

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
