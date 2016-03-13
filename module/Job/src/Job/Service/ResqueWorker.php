<?php

namespace Job\Service;

use Job\JobInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Worker
 *
 * ${CARET}
 */
class ResqueWorker extends \Resque_Worker
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    public function __construct($queues, ServiceLocatorInterface $services)
    {
        $this->services = $services;
        parent::__construct($queues);
    }

    /**
     * @return bool|null|JobInterface
     */
    public function reserve()
    {
        $queues = $this->queues();
        if (!is_array($queues)) {
            return;
        }

        foreach ($this->queues() as $queue) {
            $job = ResqueJob::reserve($queue, $this->services);
            if ($job !== false) {
                return $job;
            }
        }

        return false;
    }
}
