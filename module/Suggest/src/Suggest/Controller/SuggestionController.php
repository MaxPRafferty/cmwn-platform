<?php


namespace Suggest\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use AssetManager\Controller\ConsoleController;
use Suggest\Engine\SuggestionEngine;
use Zend\Log\Filter\Priority;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Console\Simple;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Console\Request as ConsoleRequest;

/**
 * Class SuggestionController
 * @package Suggest
 */
class SuggestionController extends ConsoleController implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * SuggestionController constructor.
     *
     * @param ServiceLocatorInterface $suggestionEngine
     */
    public function __construct($suggestionEngine)
    {
        $this->suggestionEngine = $suggestionEngine;
    }

    /**
     * @param MvcEvent $event
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch = $event->getRouteMatch();

        $writer = new Stream(STDOUT);
//        $writer->setFormatter(new Simple('%priorityName%: %message%'));
//
//        $priority = Logger::NOTICE;
//        $verbose  = $routeMatch->getParam('verbose') || $routeMatch->getParam('v');
//        $debug    = $routeMatch->getParam('debug') || $routeMatch->getParam('d');
//
//        $priority = $verbose ? Logger::INFO : $priority;
//        $priority = $debug ? Logger::DEBUG : $priority;
//        $writer->addFilter(new Priority(['priority' => $priority]));
        $this->getLogger()->addWriter($writer);

        return parent::onDispatch($event);
    }

    public function suggestAction()
    {
        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('Suggestion Engine running');
            $this->getLogger()->info('Turning on verbose');
            $this->getLogger()->debug('Turning on Debug');

            $job = $this->suggestionEngine;

            if (!$job instanceof SuggestionEngine) {
                $this->getLogger()->alert(sprintf('Invalid suggestion engine: %s', $this->suggestionEngine));

                return;
            }

            $userId = $request->getParam('user_id');
            $job->exchangeArray([
                'user_id'         => $userId,
            ]);

            //$job->setLogger($this->getLogger());

            $this->getLogger()->notice('Suggestion Engine configured.  Performing suggestions');
            $job->perform();
        } catch (\Exception $processException) {
            $this->getLogger()->emerg(
                sprintf('Error when trying to process: %s', $processException->getMessage()),
                $processException->getTrace()
            );
        }
    }
}
