<?php

namespace Import\Importer\Nyc\ClassRoom;

use Application\Exception\NotFoundException;
use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\Exception\InvalidClassRoomException;
use \ArrayObject;
use \ArrayAccess;
use \BadMethodCallException;

/**
 * Class ClassRegistry
 */
class ClassRoomRegistry implements ArrayAccess
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
    }

    /**
     * Checks the database for the group and stores it locally
     *
     * @param $classRoomId
     * @return array|bool
     * @throws InvalidClassRoomException
     */
    protected function lookUpGroup($classRoomId)
    {
        try {
            // FIXME add fetch group by external id
            $group      = $this->groupService->fetchGroup($classRoomId);
            $groupMeta  = $group->getMeta();
            $subClasses = isset($groupMeta['sub_class_rooms']) ? $groupMeta['sub_class_rooms'] : [];
            $classRoom  = new ClassRoom($group->getTitle(), $group->getExternalId(), $subClasses);

            $this->addClassroom($classRoom);
        } catch (NotFoundException $groupNotFound) {
            return false;
        }

        return $classRoom;
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

        return $this->lookUpGroup($classRoomId) !== false;
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
     * @param mixed $offset
     * @throws BadMethodCallException
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset values from the Classroom Registry');
    }
}
