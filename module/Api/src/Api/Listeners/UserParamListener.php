<?php
/**
 * Created by PhpStorm.
 * User: chaitu
 * Date: 8/10/16
 * Time: 11:13 AM
 */

namespace Api\Listeners;

use User\UserInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\ContentNegotiation\ParameterDataContainer;

/**
 * Class UserParamListener
 * @package Api\Listeners
 */
class UserParamListener
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners['Zend\Mvc\Application'] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'injectCurrentValues'],
            -630
        );
    }

    /**
     * @param SharedEventManagerInterface $manager
     */
    public function detachShared(SharedEventManagerInterface $manager)
    {
        foreach ($this->listeners as $eventId => $listener) {
            $manager->detach($eventId, $listener);
        }
    }

    /**
     * @param MvcEvent $event
     * @return null|void
     */
    public function injectCurrentValues(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return ;
        }

        if ($request->getMethod() !== Request::METHOD_PUT) {
            return;
        }

        $route  = $event->getRouteMatch();
        $userId = $route->getParam('user_id', false);


        $dataContainer = $event->getParam('ZFContentNegotiationParameterData', false);
        if (!$dataContainer instanceof ParameterDataContainer) {
            return null;
        }

        if ($userId !== false) {
            $dataContainer->setBodyParam('user_id', $userId);
        }

        $user = $event->getRouteMatch()->getParam('user');
        if (!$user instanceof UserInterface) {
            return null;
        }

        if ($user->getType() === UserInterface::TYPE_CHILD) {
            $dataContainer->setBodyParam('email', $user->getEmail());
        }
    }
}
