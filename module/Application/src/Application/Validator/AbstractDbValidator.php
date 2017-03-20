<?php

namespace Application\Validator;

use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\TableIdentifier;
use \Zend\Validator\Db\AbstractDb;
use Zend\Validator\Exception\RuntimeException;

/**
 * Abstract class for db validator
 */
abstract class AbstractDbValidator extends AbstractDb
{
    /**
     * AbstractDbValidator constructor.
     * @param array|null|\Traversable|Select $options
     */
    public function __construct($options)
    {
        parent::__construct($options);

        if (null === $this->adapter) {
            throw new RuntimeException('No database adapter present');
        }

        if (is_array($this->exclude) && !isset($this->exclude['context_field'])) {
            throw new RuntimeException('context_field not specified');
        }
    }

    /**
     * set missing values in exclude and throw runtime exception if values not found
     *
     * @param array|null $context
     */
    public function prepareExclude(array $context = null)
    {
        $exclude = parent::getExclude();

        if (!is_array($exclude)) {
            return;
        }

        $defaultValue      = $exclude['value'] ?? null;
        $contextFieldKey   = $exclude['context_field'];
        $contextFieldValue = $context[$contextFieldKey] ?? $defaultValue;

        $exclude['value'] = $contextFieldValue;
        $this->setExclude($exclude);
    }

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


        if (is_array($this->exclude)) {
            $methodName = $this->exclude['operator'] ?? 'notEqualTo';
            $select->where->$methodName(
                $this->exclude['field'],
                $this->exclude['value']
            );
        } elseif (is_string($this->exclude) || $this->exclude instanceof PredicateInterface) {
            $select->where($this->exclude);
        }

        $this->select = $select;

        return $this->select;
    }
}
