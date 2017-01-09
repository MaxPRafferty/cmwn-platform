<?php

namespace Application\Rule\Session\Action;

use Application\Exception\DuplicateEntryException;
use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;
use Zend\Session\Container;

/**
 * Class AbstractSessionAction
 */
abstract class AbstractSessionAction implements ActionInterface
{
    /**
     * @var Container
     */
    private $container;

    /**
     * Children can set if the want to allow the key to be overwritten.  If the key is set throw exception
     * @var bool
     */
    protected $allowOverwrite = true;

    /**
     * AbstractSessionAction constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    final public function __invoke(RuleItemInterface $item)
    {
        $key = $this->getSessionKey($item);
        if (!$this->allowOverwrite && $this->container->offsetExists($key)) {
            throw new DuplicateEntryException(
                sprintf('%s cannot overwrite value for %s', static::class, $key)
            );
        }

        $this->container->offsetSet($key, $this->getSessionValue($item));
    }

    /**
     * Gets the name of the key
     *
     * @param RuleItemInterface $item
     *
     * @return string
     */
    abstract protected function getSessionKey(RuleItemInterface $item): string;

    /**
     * Gets the value to write to the session
     *
     * @param RuleItemInterface $item
     *
     * @return mixed
     */
    abstract protected function getSessionValue(RuleItemInterface $item);
}
