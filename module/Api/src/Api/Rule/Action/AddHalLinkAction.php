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
     * @var bool
     */
    protected $requireOptions;

    /**
     * @var array
     */
    protected $options;



    /**
     * AddHalLinkAction constructor.
     * @param string $link
     * @param string $provider
     * @param bool $requireOptions
     * @param array $options
     */
    public function __construct(string $link, string $provider, bool $requireOptions = true, array $options = [])
    {
        $this->link = $link;
        $this->provider = $provider;
        $this->requireOptions = $requireOptions;
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

        switch ($this->requireOptions) {
            case true:
                $options = $this->options;
                array_unshift($options, $object);
                break;
            default:
                $options = [];
        }

        /**@var Link $halLink*/
        $halLink = new $this->link(...$options);
        if (!$object->getLinks()->has($halLink->getRelation())) {
            $object->getLinks()->add($halLink);
        }
    }
}
