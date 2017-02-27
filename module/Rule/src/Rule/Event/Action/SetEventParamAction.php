<?php

namespace Rule\Event\Action;

use Rule\Action\ActionInterface;
use Rule\Event\Provider\EventProvider;
use Rule\Item\RuleItemInterface;
use Rule\Utils\ProviderTypeTrait;
use Zend\EventManager\EventInterface;

/**
 * This action sets the event params according to the id and value passed to it.
 */
class SetEventParamAction implements ActionInterface
{
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var $value
     */
    protected $value;

    /**
     * SetEventParamAction constructor.
     * @param string $id
     * @param $value
     * @param string $providerName
     */
    public function __construct(string $id, $value, string $providerName = null)
    {
        $this->id = $id;
        $this->value = $value;
        $this->providerName = $providerName ?? EventProvider::PROVIDER_NAME;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        $event = $item->getParam($this->providerName);

        static::checkValueType($event, EventInterface::class);

        $event->setParam($this->id, $this->value);
    }
}
