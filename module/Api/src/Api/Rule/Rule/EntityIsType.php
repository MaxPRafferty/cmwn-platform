<?php

namespace Api\Rule\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\Object\IsTypeRule;
use Rule\Rule\RuleInterface;
use ZF\Hal\Entity;

/**
 * Checks the Entity Type in ZF\Hal\Entity
 *
 * Zf\Hal\Plugin\Hal::onRender has a ZF\Hal\Entity as the target which is a decorator
 * for the entity.  Therefore IsTypeRule will always fail since the type is mis-matched
 */
class EntityIsType extends IsTypeRule implements RuleInterface
{
    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $object = $item->getParam($this->providerName);
        $object = $object instanceof Entity ? $object->getEntity() : $object;
        $specId = $item->getSpecification()->getId();
        if ($object instanceof $this->objectType) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
