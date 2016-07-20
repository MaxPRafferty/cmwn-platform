<?php

namespace Import\Importer\Nyc\ClassRoom;

use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\Exception\InvalidClassRoomException;
use \ArrayObject;
use \ArrayAccess;
use \BadMethodCallException;
use \IteratorAggregate;
use Org\OrganizationInterface;

/**
 * Class ClassRegistry
 */
class ClassRoomRegistry implements ArrayAccess, IteratorAggregate
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var ArrayObject
     */
    protected $classRooms = [];

    /**
     * @var string
     */
    protected $organizationId;

    /**
     * ClassRoomRegistry constructor.
     *
     * @param GroupServiceInterface $groupService
     */
    public function __construct(GroupServiceInterface $groupService)
    {
        $this->groupService = $groupService;
        $this->classRooms   = new ArrayObject();
    }

    /**
     * Gets an iterator for the registry
     *
     * @return \IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator($this->classRooms);
    }

    /**
     * Sets the organization needed for group lookups
     *
     * @param OrganizationInterface|string $organization
     */
    public function setOrganization($organization)
    {
        $this->organizationId = $organization instanceof OrganizationInterface
            ? $organization->getOrgId()
            : $organization;
    }


    /**
     * @return GroupServiceInterface
     * @codeCoverageIgnore
     */
    public function getGroupService()
    {
        return $this->groupService;
    }

    /**
     * Adds a new classroom to in memory
     *
     * @param ClassRoom $classRoom
     * @throws InvalidClassRoomException
     */
    public function addClassroom(ClassRoom $classRoom)
    {
        if (!$classRoom->isValid()) {
            throw new InvalidClassRoomException('Class has invalid keys');
        }

        $this->classRooms->offsetSet($classRoom->getClassRoomId(), $classRoom);
        if (!$classRoom->isNew()) {
            return;
        }

        $group = $this->lookUpGroup($classRoom->getClassRoomId());
        if ($group !== false) {
            $classRoom->setGroup($group);
        }
    }

    /**
     * Checks the database for the group and stores it locally
     *
     * @param $classRoomId
     * @return GroupInterface|false
     * @throws InvalidClassRoomException
     */
    protected function lookUpGroup($classRoomId)
    {
        if (null === $this->organizationId) {
            throw new \RuntimeException('Lookup group called with null for organzation id');
        }

        try {
            return $this->groupService->fetchGroupByExternalId($this->organizationId, $classRoomId);
        } catch (NotFoundException $groupNotFound) {
        }
        return false;
    }

    /**
     * Tests if the class room with an Id exists
     *
     * Checks the DB if the class room has not been loaded into memory
     *
     * @todo Cache misses so that a DB call with the same ID will not need to make another call
     * @param mixed $classRoomId
     * @return bool
     */
    public function offsetExists($classRoomId)
    {
        $local = $this->classRooms->offsetExists($classRoomId);

        if ($local) {
            return true;
        }

        $group = $this->lookUpGroup($classRoomId);
        if ($group === false) {
            return false;
        }

        $groupMeta  = $group->getMeta();
        $subClasses = isset($groupMeta['sub_class_rooms']) ? $groupMeta['sub_class_rooms'] : [];
        $classRoom  = new ClassRoom($group->getTitle(), $group->getExternalId(), $subClasses);
        $classRoom->setGroup($group);
        $this->addClassroom($classRoom);
        return true;
    }

    /**
     * Returns a class room
     *
     * @param string $classRoomId
     * @return ClassRoom|null
     */
    public function offsetGet($classRoomId)
    {
        if ($this->offsetExists($classRoomId)) {
            return $this->classRooms->offsetGet($classRoomId);
        }

        return null;
    }

    /**
     * Proxy for Add class Room
     *
     * @param string $offset
     * @param ClassRoom $value
     * @throws InvalidClassRoomException
     */
    public function offsetSet($offset, $value)
    {
        $this->addClassroom($value);
    }

    /**
     * Just here to statisfiy the interface
     *
     * @param mixed $offset
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset values from the Classroom Registry');
    }
}
