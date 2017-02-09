<?php

namespace Application\Utils;

use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Where;

/**
 * Trait ServiceTrait
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
            $this->aliasKeys($where);
            $set = new PredicateSet();
            $set->addPredicates($where);

            $where = $set;
        }

        return !$where instanceof PredicateInterface ? new Where() : $where;
    }

    /**
     * Runs through all the keys in an array to set the alias on the key
     *
     * @param array $where
     */
    protected function aliasKeys(array &$where)
    {
        if (empty($this->getAlias())) {
            return;
        }

        foreach ($where as $field => $value) {
            unset($where[$field]);
            $where[$this->getAlias() . '.' . $field] = $value;
        }
    }

    /**
     * Any class using this trait can return the the table alias here
     *
     * @return string
     */
    public function getAlias(): string
    {
        return '';
    }
}
