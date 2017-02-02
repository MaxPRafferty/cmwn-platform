<?php

namespace Api\Rule\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use ZF\Hal\Link\LinkCollectionAwareInterface;
use ZF\Hal\Link\Link;

/**
 * This class adds a hal link to Links aware collection interface
 */
class AddHalLinkAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var string
     */
    protected $provider;

    /**
     * @var array
     */
    protected $options;

    /**
     * AddHalLinkAction constructor.
     * @param string $link
     * @param string $provider
     * @param array $options
     */
    public function __construct(string $link, string $provider, array $options = [])
    {
        $this->link = $link;
        $this->provider = $provider;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $object = $item->getParam($this->provider);

        if (!$object instanceof LinkCollectionAwareInterface) {
            return;
        }

        $options = $this->options;
        array_unshift($options, $object);

        /**@var Link $halLink*/
        $halLink = new $this->link(...$options);
        if (!$object->getLinks()->has($halLink->getRelation())) {
            $object->getLinks()->add($halLink);
        }
    }
}
