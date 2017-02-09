<?php

namespace Application\Utils;

use Application\Exception\NotFoundException;
use Application\Utils\Date\SoftDeleteInterface;
use Zend\Db\Sql\Predicate\IsNull;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;

/**
 * Class HideDeletedEntityListener
 *
 * @todo Allow some entities to be able to see deleted entities
 */
class HideDeletedEntitiesListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var string name of the deleted field in the table
     */
    protected $deletedField = 'deleted';

    /**
     * @var array events to listen too to add a where
     */
    protected $whereEvents = [];

    /**
     * @var string name of the event param key that will contain the where
     */
    protected $whereParamKey = 'where';

    /**
     * @var string name of the param key to fetch the entity
     */
    protected $entityParamKey = 'entity';

    /**
     * @var array events to listen on that will produce an entity
     */
    protected $entityEvents = [];

    /**
     * @var string
     */
    protected $table;

    /**
     * HideDeletedEntitiesListener constructor.
     *
     * @param array $whereEvents
     * @param array $entityEvents
     * @param null $table
     */
    public function __construct(array $whereEvents, array $entityEvents, $table = null)
    {
        $this->whereEvents  = $whereEvents;
        $this->entityEvents = $entityEvents;
        $this->table        = $table;
    }

    /**
     * @param string $deletedField
     * @return HideDeletedEntitiesListener
     */
    public function setDeletedField($deletedField)
    {
        $this->deletedField = $deletedField;
        return $this;
    }

    /**
     * @param array $whereEvents
     * @return HideDeletedEntitiesListener
     */
    public function setWhereEvents($whereEvents)
    {
        $this->whereEvents = $whereEvents;

        return $this;
    }

    /**
     * @param string $whereParamKey
     * @return HideDeletedEntitiesListener
     */
    public function setWhereParamKey($whereParamKey)
    {
        $this->whereParamKey = $whereParamKey;
        return $this;
    }

    /**
     * @param string $entityParamKey
     * @return HideDeletedEntitiesListener
     */
    public function setEntityParamKey($entityParamKey)
    {
        $this->entityParamKey = $entityParamKey;
        return $this;
    }

    /**
     * @param array $entityEvents
     * @return HideDeletedEntitiesListener
     */
    public function setEntityEvents($entityEvents)
    {
        $this->entityEvents = $entityEvents;

        return $this;
    }

    /**
     * @return string
     */
    protected function getDeletedField()
    {
        if ($this->table !== null) {
            return $this->table . '.' . $this->deletedField;
        }

        return $this->deletedField;
    }

    /**
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        foreach ($this->whereEvents as $eventName) {
            $this->listeners[] = $events->attach($eventName, [$this, 'addPredicateToWhere']);
        }

        foreach ($this->entityEvents as $eventName) {
            $this->listeners[] = $events->attach($eventName, [$this, 'hideEntity']);
        }
    }

    /**
     * Adds the where exclusion
     *
     * @param Event $event
     */
    public function addPredicateToWhere(Event $event)
    {
        $where = $event->getParam($this->whereParamKey);
        if (!$where instanceof PredicateSet) {
            return;
        }

        $where->addPredicate(new IsNull($this->getDeletedField()));
    }

    /**
     * Checks if the entity is deleted and throws not found
     *
     * @param Event $event
     * @throws NotFoundException
     */
    public function hideEntity(Event $event)
    {
        $entity = $event->getParam($this->entityParamKey);
        if (!$entity instanceof SoftDeleteInterface) {
            return;
        }

        if ($entity->isDeleted()) {
            throw new NotFoundException('Entity not found');
        }
    }
}
