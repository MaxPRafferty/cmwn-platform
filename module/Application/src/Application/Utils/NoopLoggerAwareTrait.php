<?php

namespace Application\Utils;

use Zend\Log\Logger;
use Zend\Log\LoggerInterface;

/**
 * Trait NoopLoggerAwareTrait
 */
trait NoopLoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface|Logger
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        }

        return $this->logger;
    }
}
