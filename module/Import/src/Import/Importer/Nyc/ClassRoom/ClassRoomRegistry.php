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
            // TODO add fetch group by external id
            $group     = $this->groupService->fetchGroup($classRoomId);
            $groupMeta = $group->getMeta();
            $subClasses = isset($groupMeta['sub_class_rooms']) ? $groupMeta['sub_class_rooms'] : [];
            $classRoom = [
                'title'           => $group->getTitle(),
                'class_id'        => $group->getExternalId(),
                'sub_class_rooms' => $subClasses,
            ];

            $this->addClassroom($classRoom);
        } catch (NotFoundException $groupNotFound) {
            return false;
        }

        return $classRoom;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        $local = $this->classRooms->offsetExists($offset);

        if ($local) {
            return true;
        }

        return $this->lookUpGroup($offset) !== false;
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($this->classRooms->offsetExists($offset)) {
            return $this->classRooms->offsetGet($offset);
        }

        return null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->addClassroom($value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset values from the Classroom Registry');
    }
}
