<?php

namespace Api\Rule\Action;

use Rule\Action\ActionInterface;
use Rule\Exception\RuntimeException;
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
    public function __construct(string $exceptionClass, string $exceptionMessage = null, int $exceptionCode = null)
    {
        $this->exceptionClass = $exceptionClass;
        $this->exceptionCode = $exceptionCode;
        $this->exceptionMessage = $exceptionMessage;

        if (!class_exists($exceptionClass) || !in_array(\Throwable::class, class_implements($exceptionClass))) {
            throw new RuntimeException("Illegal class name for exception class: " . $exceptionClass);
        }
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
