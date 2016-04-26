<?php

namespace Flip\Delegator;

use Application\Utils\ServiceTrait;
use Flip\Service\FlipUserService;
use Flip\Service\FlipUserServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FlipUserDelegator
 */
class FlipUserDelegator implements FlipUserServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var FlipUserService
     */
    protected $realService;

    /**
     * FlipDelegator constructor.
     *
     * @param FlipUserService $flipService
     */
    public function __construct(FlipUserService $flipService)
    {
        $this->realService = $flipService;
    }

    /**
     * Fetches all the earned flips for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchEarnedFlipsForUser($user, $where = null, $prototype = null)
    {
        $where    = $this->createWhere($where);
        $event    = new Event(
            'fetch.user.flips',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype, 'user' => $user]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchEarnedFlipsForUser($user, $where, $prototype);
        $event->setName('fetch.user.flips.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }

    /**
     * Attaches a flip to a user
     *
     * @param $user
     * @param $flip
     * @return bool
     */
    public function attachFlipToUser($user, $flip)
    {
        $event    = new Event(
            'attach.flip',
            $this->realService,
            ['user' => $user, 'flip' => $flip]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchEarnedFlipsForUser($user, $flip);
        $event->setName('attach.flip.post');
        $event->setParam('flips', $return);
        $this->getEventManager()->trigger($event);

        return $return;
    }
}
