<?php

namespace SuggestCron\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use Job\JobInterface;
use Job\Service\JobServiceInterface;
use Security\Authentication\AuthenticationServiceAwareInterface;
use Security\Authentication\AuthenticationServiceAwareTrait;
use Security\SecurityUser;
use User\Service\UserServiceInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractConsoleController as ConsoleController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;
use Zend\Log\Filter\Priority;
use Zend\Log\Formatter\Simple;
use Zend\Log\Writer\Stream;
use Suggest\Engine\SuggestionEngine;

/**
 * Class SuggestCronController
 * @package SuggestCron\Controller
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuggestCronController extends ConsoleController implements
    LoggerAwareInterface,
    AuthenticationServiceAwareInterface
{
    use AuthenticationServiceAwareTrait;
    use NoopLoggerAwareTrait;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * SuggestCronController constructor.
     * @param UserServiceInterface $userService
     * @param SuggestionEngine $suggestionEngine
     * @param AuthenticationService $authenticationService
     * @param JobServiceInterface $jobService
     */
    public function __construct(
        UserServiceInterface $userService,
        SuggestionEngine $suggestionEngine,
        AuthenticationService $authenticationService,
        JobServiceInterface $jobService
    ) {
        $this->userService = $userService;
        $this->suggestionEngine = $suggestionEngine;
        $this->setAuthenticationService($authenticationService);
        $this->jobService = $jobService;
    }

    /**
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

    public function suggestCronAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('Suggest Cron Job running');
            $this->getLogger()->info('Turning on verbose');
            $this->getLogger()->debug('Turning on Debug');

            $users = $this->userService->fetchAll();
            $users = $users->getItems(0, $users->count());



            if (!$this->suggestionEngine instanceof JobInterface) {
                $this->getLogger()->alert(sprintf('Invalid suggestion engine: %s', $this->suggestionEngine));

                return;
            }

            /**@var \User\UserInterface $user*/
            foreach ($users as $user) {
                $job = $this->suggestionEngine;
                $job->exchangeArray([
                   'user_id' => $user->getUserId(),
                ]);

                $this->getLogger()->notice(
                    'Suggestion Engine configured.  Performing suggestions for user: ' . $user->getUserName()
                );
                $this->jobService->sendJob($job);
            }
        } catch (\Exception $processException) {
            $this->getLogger()->emerg(
                sprintf('Error when trying to process: %s', $processException->getMessage()),
                $processException->getTrace()
            );
        }
    }
}
