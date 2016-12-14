<?php

namespace Application\Controller;

use Application\Utils\NoopLoggerAwareTrait;
use Zend\Cache\Storage\Adapter\Redis;
use Zend\Log\Filter\Priority;
use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\Writer\Stream;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\Cache\Storage\StorageInterface as CacheStorage;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\MvcEvent;
use Zend\Log\Formatter\Simple;

/**
 * Class RedisController
 * @package Application\Controller
 * @SuppressWarnings(PHPMD)
 */
class RedisController extends AbstractConsoleController implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var CacheStorage
     */
    protected $cacheStorage;

    /**
     * RedisController constructor.
     * @param CacheStorage $cacheStorage
     */
    public function __construct(CacheStorage $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }
    /**
     * @inheritdoc
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

        parent::onDispatch($event);
    }

    public function redisDeleteAction()
    {
        if (!$this->cacheStorage instanceof Redis) {
            $this->getLogger()->notice('Redis extension not loaded. Request cannot be performed.');
            return;
        }

        try {
            $request = $this->getRequest();
            if (!$request instanceof ConsoleRequest) {
                throw new \RuntimeException('Invalid Request');
            }

            $this->getLogger()->notice('Redis controller running');
            $this->getLogger()->info('turning on verbose');
            $this->getLogger()->debug('turning on debug');

            $all = $request->getParam('all', false);
            $key = $request->getParam('key', false);

            $option = $all ? 'all' : ($key ? 'key' : false);

            switch ($option) {
                case 'all':
                    $this->getLogger()->notice("flushing cache storage");
                    $this->cacheStorage->flush();
                    $this->getLogger()->notice("cache emptied");
                    break;
                case 'key':
                    if (!$this->cacheStorage->hasItem($key)) {
                        $this->getLogger()->notice("key doesn't exist");
                        break;
                    }
                    $this->cacheStorage->removeItem($key);
                    break;
                default:
                    $this->getLogger()->notice('Invalid option given. Choose between [--all, --key=]');
            }
        } catch (\Exception $processException) {
            $this->getLogger()->emerg(
                sprintf('Error when trying to process: %s', $processException->getMessage()),
                $processException->getTrace()
            );
        }
    }
}
