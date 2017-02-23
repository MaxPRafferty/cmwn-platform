<?php

namespace Api\Rule\Action;

use Rule\Action\ActionInterface;
use Rule\Item\RuleItemInterface;

/**
 * Throws an exception of given type with message and code if provided
 */
class ThrowException implements ActionInterface
{
    /**
     * @var string
     */
    protected $exceptionClass;

    /**
     * @var string
     */
    protected $exceptionMessage;

    /**
     * @var integer
     */
    protected $exceptionCode;

    /**
     * ThrowException constructor.
     * @param string $exceptionClass
     * @param string $exceptionMessage
     * @param integer $exceptionCode
     */
    public function __construct(string $exceptionClass, $exceptionMessage = '', $exceptionCode = 0)
    {
        $this->exceptionClass = $exceptionClass;
        $this->exceptionCode = $exceptionCode;
        $this->exceptionMessage = $exceptionMessage;
    }

    /**
     * @inheritdoc
     */
    public function __invoke(RuleItemInterface $item)
    {
        /**@var \Exception*/
        $exception = new $this->exceptionClass($this->exceptionMessage, $this->exceptionCode);

        throw $exception;
    }
}
