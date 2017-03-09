<?php

namespace Job\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use Interop\Container\ContainerInterface;
use Job\Service\ResqueWorker;
use Security\Authentication\AuthenticationService;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Console\Request as ConsoleRequest;
use Zend\Log\Filter\Priority;
use Zend\Log\Formatter\Simple;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Console\Controller\AbstractConsoleController as ConsoleController;
use Zend\Mvc\MvcEvent;

/**
 * Class WorkerController
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WorkerController extends ConsoleController implements LoggerAwareInterface, AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use NoopLoggerAwareTrait;

    /**
     * @var ContainerInterface
     */
    protected $services;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * WorkerController constructor.
     *
     * @param ContainerInterface $services
     * @param AuthenticationService $authenticationService
     */
    public function __construct(ContainerInterface $services, AuthenticationService $authenticationService)
    {
        $this->services     = $services;
        $this->setAuthenticationService($authenticationService);
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

        $user = new SecurityUser(['super' => 1, 'username' => 'Import Processor']);
        $auth = $this->getAuthenticationService();
        if ($auth instanceof AuthenticationService) {
            $auth->setStorage(new NonPersistent());
            $auth->getStorage()->write($user);
        }

        return parent::onDispatch($event);
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

        $queue    = [$request->getParam('queue', 'default')];
        $interval = $request->getParam('interval', 5);
        $worker   = new ResqueWorker($queue, $this->services);
        $worker->setLogger($this->getLogger());

        $this->getLogger()->notice('Starting Worker');
        $worker->work($interval);
    }
}
