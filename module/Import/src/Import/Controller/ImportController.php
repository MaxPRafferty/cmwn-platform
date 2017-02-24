<?php

namespace Import\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use Import\ImporterInterface;
use Import\ProcessorErrorException;
use Interop\Container\ContainerInterface;
use Job\Feature\DryRunInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use Zend\Authentication\AuthenticationService;
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
 * Class ImportController
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ImportController extends ConsoleController implements LoggerAwareInterface, AuthenticationServiceAwareInterface
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
     * ImportController constructor.
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
     * Imports an Excel Sheet into the system
     */
    public function importAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('File Importer running');
            $this->getLogger()->info('Turning on verbose');
            $this->getLogger()->debug('Turning on Debug');
            $type = $request->getParam('type');
            if (!$this->services->has($type)) {
                $this->getLogger()->crit(sprintf('Importer "%s" not found in services: ', $type));

                return;
            }

            $job = $this->services->get($type);

            if (!$job instanceof ImporterInterface) {
                $this->getLogger()->alert(sprintf('Invalid importer: %s', $type));

                return;
            }

            if ($job instanceof DryRunInterface) {
                $job->setDryRun($request->getParam('dry-run', false));
            }

            $job->setLogger($this->getLogger());

            $job->exchangeArray([
                'file'         => $request->getParam('file'),
                'teacher_code' => $request->getParam('teacherCode'),
                'student_code' => $request->getParam('studentCode'),
                'school'       => $request->getParam('school'),
                'email'        => $request->getParam('email'),
                'code_start'   => $request->getParam('codeStart'),
            ]);

            $this->getLogger()->notice('Importer configured.  Performing import');
            $job->perform();
        } catch (ProcessorErrorException $processException) {
            $this->getLogger()->info(
                $processException->getMessage(),
                ['trace' => $processException->getTraceAsString()]
            );
        } catch (\Throwable $processException) {
            $this->getLogger()->crit(
                sprintf('Error when trying to process: %s', $processException->getMessage()),
                ['exception' => $processException]
            );
        }
    }
}
