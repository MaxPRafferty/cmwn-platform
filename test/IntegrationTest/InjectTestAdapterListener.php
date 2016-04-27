<?php

namespace IntegrationTest;

use Zend\Db\Adapter\Adapter;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\ServiceManager;

/**
 * Class InjectTestAdapterListener
 *
 * ${CARET}
 */
class InjectTestAdapterListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'injectTestAdapter']);
    }

    public static function injectTestAdapter(MvcEvent $event)
    {
        $adapter  = static::getTestDbAdapter();
        $services = $event->getApplication()->getServiceManager();

        if (!$services instanceof ServiceManager) {
            return;
        }

        $services->setAllowOverride(true);
        $services->setService('Zend\Db\Adapter\Adapter', $adapter);
    }

    /**
     * @return Adapter
     */
    public static function getTestDbAdapter()
    {
        return new Adapter(TestHelper::getTestDbConfig());
    }
}
