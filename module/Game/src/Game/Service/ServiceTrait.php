<?php

namespace Game\Service;

use Game\GameInterface;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Where;

/**
 * Trait ServiceTrait
 */
trait ServiceTrait
{
    protected static $flagMap = [
        'global'      => GameInterface::GAME_GLOBAL,
        'featured'    => GameInterface::GAME_FEATURED,
        'coming_soon' => GameInterface::GAME_COMING_SOON,
        'desktop'     => GameInterface::GAME_DESKTOP,
        'unity'       => GameInterface::GAME_UNITY,
    ];

    /**
     * @param $where
     *
     * @return PredicateInterface|PredicateSet|Where
     */
    public function createWhere($where)
    {
        if (!empty($where) && is_array($where)) {
            // pull out the flag keys
            $flagWhere = array_intersect_key(
                $where,
                array_flip(['global', 'coming_soon', 'featured', 'unity', 'desktop'])
            );

            // now remove the flag keys
            $where = array_diff_key($where, $flagWhere);
            $this->aliasKeys($where);
            $set = new PredicateSet();
            $set->addPredicates($where);

            $flagField = empty($this->getAlias())
                ? 'flags'
                : $this->getAlias() . '.flags';
            // Add the flags as an or

            array_walk($flagWhere, function ($value, $flag) use (&$set, &$flagField) {
                $bit = static::$flagMap[$flag] ?? null;
                if ($bit === null) {
                    // skip if flag not defined
                    return;
                }

                $expression = $value
                    ? new Expression($flagField .' & ? = ?', $bit, $bit)
                    : new Expression($flagField . ' & ? != ?', $bit, $bit);

                $set->orPredicate($expression);
            });

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
