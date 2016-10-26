<?php

namespace Flag\Service;

use Flag\FlagInterface;
use Zend\Db\Sql\Where;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface FlagServiceInterface
 * @package Flag\Service
 */
interface FlagServiceInterface
{
    /**
     * @param Where|null $where
     * @param $prototype
     * @return DbSelect
     */
    public function fetchAll($where = null, $prototype = null);

    /**
     * @param FlagInterface $flag
     * @return bool
     */
    public function saveFlag(FlagInterface $flag);

    /**
     * @param $flagId
     * @param $prototype
     * @return FlagInterface
     * @internal param $flagger
     * @internal param $flaggee
     * @internal param $url
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchFlag($flagId, $prototype = null);

    /**
     * @param FlagInterface $flag
     * @return bool
     */
    public function updateFlag(FlagInterface $flag);

    /**
     * @param FlagInterface $flag
     * @return bool
     */
    public function deleteFlag(FlagInterface $flag);
}
