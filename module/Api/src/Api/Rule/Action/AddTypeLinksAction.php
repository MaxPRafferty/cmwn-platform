<?php

namespace Api\Rule\Action;

use Group\GroupInterface;
use Org\OrganizationInterface;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use ZF\Hal\Link\Link;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * An Action that will add
 */
class AddTypeLinksAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $typeProviderName = '';

    /**
     * @var string
     */
    protected $entityProviderName = '';

    /**
     * AddGroupTypeLinks constructor.
     *
     * @param string $link
     * @param string $typeProviderName
     * @param string $entityProviderName
     */
    public function __construct(string $link, string $typeProviderName, string $entityProviderName)
    {
        $this->link               = $link;
        $this->typeProviderName   = $typeProviderName;
        $this->entityProviderName = $entityProviderName;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $types  = $item->getParam($this->typeProviderName);
        $entity = $item->getParam($this->entityProviderName);

        if (!$entity instanceof LinkCollectionAwareInterface) {
            return;
        }

        foreach ($types as $type) {
            $options = [$type, $entity];

            /**@var Link $link */
            $link = new $this->link(...$options);
            if (!$entity->getLinks()->has($link->getRelation())) {
                $entity->getLinks()->add($link);
            }
        }
    }
}
