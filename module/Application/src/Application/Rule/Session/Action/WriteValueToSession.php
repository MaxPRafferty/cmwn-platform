<?php

namespace Application\Rule\Session\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Zend\Session\Container;

/**
 * Writes a static value to the session
 */
class WriteValueToSession extends AbstractSessionAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container, string $name, string $value)
    {
        parent::__construct($container);
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    protected function getSessionKey(RuleItemInterface $item): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    protected function getSessionValue(RuleItemInterface $item)
    {
        return $this->value;
    }
}
