<?php

namespace Import\Controller;

use Import\ImporterInterface;
use Job\Service\ResqueWorker;
use Zend\Console\Request as ConsoleRequest;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractConsoleController as ConsoleController;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportController
 */
class ImportController extends ConsoleController implements LoggerAwareInterface
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
     * @param ResqueWorker $worker
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

    public function importAction()
    {
        $request = $this->getRequest();
        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('Invalid Request');
        }

        $this->getLogger()->addWriter(new Stream(STDOUT));

        $this->getLogger()->notice('File Importer');
        $type = $request->getParam('type');
        if (!$this->services->has($type)) {
            $this->getLogger()->alert(sprintf('Importer "%s" not found in services: ', $type));
            return;
        }

        $job  = $this->services->get($type);

        if (!$job instanceof ImporterInterface) {
            $this->getLogger()->alert(sprintf('Invalid importer: %s', $type));
            return;
        }

        $file        = $request->getParam('file');
        $teacherCode = $request->getParam('teacherCode');
        $studentCode = $request->getParam('studentCode');
    }
}
