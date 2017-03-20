<?php

namespace Application\Validator;

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
    }

    /**
     * set missing values in exclude and throw runtime exception if values not found
     * @param $context
     * @return array|string
     * @throws RuntimeException
     */
    public function prepareExclude($context)
    {
        $exclude = parent::getExclude();

        if (is_array($exclude) && !isset($exclude['value'])) {
            if (isset($exclude['context_field'])) {
                if (!isset($context[$exclude['context_field']])) {
                    throw new RuntimeException("could not resolve value for context field");
                }

                $fieldValue = $context[$exclude['context_field']];
            }

            if (!isset($fieldValue)) {
                if (!isset($exclude['field']) || !isset($context[$exclude['field']])) {
                    throw new RuntimeException("could not resolve value for field");
                }

                $fieldValue = $context[$exclude['field']];
            }

            $exclude['value'] = $fieldValue;
            $this->setExclude($exclude);
        }
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
