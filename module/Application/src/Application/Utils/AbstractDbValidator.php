<?php

namespace Application\Utils;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\TableIdentifier;
use \Zend\Validator\Db\AbstractDb;

abstract class AbstractDbValidator extends AbstractDb
{
    /**
     * @inheritdoc
     */
    public function getSelect()
    {
        if ($this->select instanceof Select) {
            return $this->select;
        }

        // Build select object
        $select          = new Select();
        $tableIdentifier = new TableIdentifier($this->table, $this->schema);
        $select->from($tableIdentifier)->columns([$this->field]);
        $select->where->equalTo($this->field, null);

        if ($this->exclude !== null) {
            if (is_array($this->exclude)) {
                $methodName = $this->exclude['operator'] ?? 'notEqualTo';
                $select->where->$methodName(
                    $this->exclude['field'],
                    $this->exclude['value']
                );
            } else {
                $select->where($this->exclude);
            }
        }

        $this->select = $select;

        return $this->select;
    }
}
