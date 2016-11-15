<?php

namespace Api\V1\Rest\RestoreDb;

use RestoreDb\Service\RestoreDbServiceInterface;
use ZF\Rest\AbstractResourceListener;

/**
 * Class RestoreDbResource
 * @package Api\V1\Rest\RestoreDb
 */
class RestoreDbResource extends AbstractResourceListener
{
    /**
     * @var RestoreDbServiceInterface
     */
    protected $restoreDbService;

    /**
     * RestoreDbResource constructor.
     * @param RestoreDbServiceInterface $restoreDbService
     */
    public function __construct(RestoreDbServiceInterface $restoreDbService)
    {
        $this->restoreDbService = $restoreDbService;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($params = [])
    {
        $this->restoreDbService->runDbStateRestorer();
        return [];
    }
}
