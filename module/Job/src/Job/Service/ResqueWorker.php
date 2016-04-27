<?php

namespace Job\Service;

use Job\JobInterface;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Worker
 * @codeCoverageIgnore
 */
class ResqueWorker extends \Resque_Worker implements LoggerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ResqueWorker constructor.
     * @param array|string $queues
     * @param ServiceLocatorInterface $services
     */
    public function __construct($queues, ServiceLocatorInterface $services)
    {
        $this->services = $services;
        parent::__construct($queues);
    }

    /**
     * Set logger instance
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        }

        return $this->logger;
    }

    /**
     * @param string $message
     */
    public function log($message)
    {
        $this->getLogger()->debug($message);
    }

    /**
     * @return bool|null|JobInterface
     */
    public function reserve()
    {
        $queues = $this->queues();
        if (!is_array($queues)) {
            $this->getLogger()->alert('No Queues defined, quitting');
            return null;
        }

        foreach ($this->queues() as $queue) {
            $job = ResqueJob::reserveJob($queue, $this->services);
            if ($job !== false) {
                $job->setLogger($this->getLogger());
                return $job;
            }
        }

        return false;
    }
}
