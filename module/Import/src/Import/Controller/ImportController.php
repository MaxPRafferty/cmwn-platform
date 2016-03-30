<?php

namespace Import\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use Import\ImporterInterface;
use Job\Feature\DryRunInterface;
use Zend\Console\Request as ConsoleRequest;
use Zend\Log\Filter\Priority;
use Zend\Log\Formatter\Simple;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractConsoleController as ConsoleController;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportController
 * @codeCoverageIgnore
 */
class ImportController extends ConsoleController implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * ImportController constructor.
     * @param ServiceLocatorInterface $services
     */
    public function __construct(ServiceLocatorInterface $services)
    {
        $this->services     = $services;
    }

    /**
     * Sets the logging level
     *
     * @param MvcEvent $event
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        $writer = new Stream(STDOUT);
        $writer->setFormatter(new Simple('%priorityName%: %message%'));

        $priority = Logger::NOTICE;
        $verbose  = $routeMatch->getParam('verbose') || $routeMatch->getParam('v');
        $debug    = $routeMatch->getParam('debug') || $routeMatch->getParam('d');

        $priority = $verbose ? Logger::INFO : $priority;
        $priority = $debug ? Logger::DEBUG : $priority;
        $writer->addFilter(new Priority(['priority' => $priority]));
        $this->getLogger()->addWriter($writer);
        return parent::onDispatch($event);
    }

    public function importAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('File Importer');
            $this->getLogger()->info('Turning on verbose');
            $this->getLogger()->debug('Turning on Debug');
            $type = $request->getParam('type');
            if (!$this->services->has($type)) {
                $this->getLogger()->alert(sprintf('Importer "%s" not found in services: ', $type));

                return;
            }

            $job = $this->services->get($type);

            if (!$job instanceof ImporterInterface) {
                $this->getLogger()->alert(sprintf('Invalid importer: %s', $type));

                return;
            }

            if ($job instanceof DryRunInterface) {
                $job->setDryRun($request->getParam('dry-run'));
            }

            $job->exchangeArray([
                'file'         => $request->getParam('file'),
                'teacher_code' => $request->getParam('teacherCode'),
                'student_code' => $request->getParam('studentCode'),
                'school'       => $request->getParam('school'),
                'email'        => $request->getParam('email'),
            ]);

            $job->setLogger($this->getLogger());

            $this->getLogger()->info('Running importer');
            $job->perform();
        } catch (\Exception $processException) {
            $this->getLogger()->emerg(
                'Error when trying to process: ' . $processException->getMessage(),
                $processException->getTrace()
            );
        }
    }
}
