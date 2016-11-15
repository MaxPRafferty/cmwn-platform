<?php

namespace RestoreDb\Delegator;

use Application\Utils\ServiceTrait;
use RestoreDb\Service\RestoreDbService;
use RestoreDb\Service\RestoreDbServiceInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;

/**
 * Class RestoreDbDelegator
 * @package RestoreDb\Delegator
 */
class RestoreDbDelegator implements RestoreDbServiceInterface
{
    use EventManagerAwareTrait;
    use ServiceTrait;

    /**
     * @var array
     */
    protected $eventIdentifier = [RestoreDbServiceInterface::class];

    /**
     * @var RestoreDbService
     */
    protected $realService;

    /**
     * RestoreDbDelegator constructor.
     * @param RestoreDbService $restoreDbService
     */
    public function __construct(RestoreDbService $restoreDbService)
    {
        $this->realService = $restoreDbService;
    }

    /**
     * @inheritdoc
     */
    public function runDbStateRestorer()
    {
        $eventParams = [];
        $event = new Event('restore.db.state', $this->realService, $eventParams);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $this->realService->runDbStateRestorer();
            $event->setName('restore.db.state.post');
            $this->getEventManager()->trigger($event);
        } catch (\Exception $exception) {
            $event->setName('restore.db.state.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->trigger($event);
            throw $exception;
        }
    }
}
