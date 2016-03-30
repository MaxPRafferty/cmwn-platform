<?php

namespace Application\Utils;

use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Where;

/**
 * Trait ServiceTrait
 *
 * ${CARET}
 */
trait ServiceTrait
{
    /**
     * @param $where
     * @return PredicateInterface|PredicateSet|Where
     */
    public function createWhere($where)
    {
        if (is_array($where)) {
            $set = new PredicateSet();
            $set->addPredicates($where);

            $where = $set;
        }

        return !$where instanceof PredicateInterface ? new Where() : $where;
    }
}
