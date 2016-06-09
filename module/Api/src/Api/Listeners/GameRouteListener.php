<?php

namespace Api\Listeners;

use Application\Exception\NotFoundException;
use Game\Service\GameServiceInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class GameRouteListener
 */
class GameRouteListener
{
    /**
     * @var GameServiceInterface
     */
    protected $gameService;

    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * GameRouteListener constructor.
     *
     * @param GameServiceInterface $service
     */
    public function __construct(GameServiceInterface $service)
    {
        $this->gameService = $service;
    }

    /**
     * @param SharedEventManagerInterface $events
     */
    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners['Zend\Mvc\Application'] = $events->attach(
            'Zend\Mvc\Application',
            MvcEvent::EVENT_ROUTE,
            [$this, 'onRoute']
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
     * @return void|ApiProblem
     */
    public function onRoute(MvcEvent $event)
    {
        $request = $event->getRequest();
        if (!$request instanceof Request) {
            return null;
        }

        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            return null;
        }

        $route  = $event->getRouteMatch();
        $gameId = $route->getParam('game_id', false);

        if ($gameId === false) {
            return null;
        }

        try {
            $user = $this->gameService->fetchGame($gameId);
        } catch (NotFoundException $notFound) {
            return new ApiProblemResponse(new ApiProblem(404, 'Game not found'));
        }

        $route->setParam('game', $user);
        return null;
    }
}
