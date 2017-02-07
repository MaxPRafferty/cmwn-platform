<?php

namespace Application\Rule\Session\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Zend\Session\Container;

/**
 * Writes a provider value to the session
 */
class WriteProviderToSession extends AbstractSessionAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $providerName;

    /**
     * @inheritDoc
     */
    public function __construct(Container $container, string $providerName)
    {
        parent::__construct($container);
        $this->providerName = $providerName;
    }

    /**
     * @inheritDoc
     */
    protected function getSessionKey(RuleItemInterface $item): string
    {
        return $this->providerName;
    }

    /**
     * @inheritDoc
     */
    protected function getSessionValue(RuleItemInterface $item)
    {
        return $item->getParam($this->providerName);
    }
}
