<?php

namespace Flip\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Flip\FlipInterface;
use Flip\Service\FlipService;
use Flip\Service\FlipServiceInterface;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Class FlipDelegator
 */
class FlipDelegator implements FlipServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var FlipService
     */
    protected $realService;

    /**
     * FlipDelegator constructor.
     *
     * @param FlipService $flipService
     */
    public function __construct(FlipService $flipService)
    {
        $this->realService = $flipService;
    }

    /**
     * Fetches all the flips
     *
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return AdapterInterface
     */
    public function fetchAll($where = null, $prototype = null)
    {
        $where    = $this->createWhere($where);
        $event    = new Event(
            'fetch.all.flips',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $prototype);
        $event->setName('fetch.all.flips.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Fetches a flip by the flip Id
     *
     * @param $flipId
     * @return FlipInterface
     * @throws NotFoundException
     */
    public function fetchFlipById($flipId)
    {
        $event    = new Event('fetch.flip', $this->realService, ['flip_id' => $flipId]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchFlipById($flipId);
        $event->setParam('flip', $return);
        $event->setName('fetch.flip.post');
        $this->getEventManager()->trigger($event);
        return $return;
    }
}
