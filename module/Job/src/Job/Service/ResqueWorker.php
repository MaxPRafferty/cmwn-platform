<?php

namespace Job\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Job\JobInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Worker
 * @codeCoverageIgnore
 */
class ResqueWorker extends \Resque_Worker implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

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
                $this->getLogger()->notice('Got a job');
                $job->setLogger($this->getLogger());
                return $job;
            }
        }

        return false;
    }
}
