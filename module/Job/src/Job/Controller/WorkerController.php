<?php

namespace Job\Controller;

use Job\Service\ResqueWorker;
use PHPMD\Writer\StreamWriter;
use Zend\Console\Request as ConsoleRequest;
use Zend\Log\Filter\Priority;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractConsoleController as ConsoleController;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class WorkerController
 * @codeCoverageIgnore
 */
class WorkerController extends ConsoleController implements LoggerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * WorkerController constructor.
     * @param ServiceLocatorInterface $services
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services = $services;
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
     * @return Logger
     */
    public function getLogger()
    {
        if ($this->logger === null) {
            $this->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        }

        return $this->logger;
    }

    /**
     * Waits for something to get popped into the Queue
     */
    public function workAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Invalid Request');
        }

        $writer = new Stream(STDOUT);
        $writer->addFilter(new Priority(Logger::INFO));
        $this->getLogger()->addWriter($writer);

        $queue    = [$request->getParam('queue', 'default')];
        $interval = $request->getParam('interval', 5);
        $worker   = new ResqueWorker($queue, $this->services);
        $worker->setLogger($this->getLogger());

        $this->getLogger()->notice('Starting Worker');
        $worker->work($interval);
    }
}
