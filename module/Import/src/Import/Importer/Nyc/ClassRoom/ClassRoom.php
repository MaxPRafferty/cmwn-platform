<?php

namespace Import\Importer\Nyc\ClassRoom;

use Group\GroupInterface;

/**
 * Class ClassRoom
 */
class ClassRoom
{
    /**
     * @var string name of the class room
     */
    protected $title;

    /**
     * @var string id of the class
     */
    protected $classRoomId;

    /**
     * @var string[] list of sub class rooms
     */
    protected $subClassRooms = [];

    /**
     * @var GroupInterface our internal group
     */
    protected $group;

    /**
     * ClassRoom constructor.
     *
     * @param $title
     * @param $classRoomId
     * @param array $subClasses
     */
    public function __construct($title, $classRoomId, array $subClasses = [], GroupInterface $group = null)
    {
        $this->setTitle($title);
        $this->setClassRoomId($classRoomId);
        $this->setSubClassRooms($subClasses);

        if ($group !== null) {
            $this->setGroup($group);
        }
    }

    /**
     * Tests if this is a new class room that will be saved
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->group === null;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getClassRoomId()
    {
        return $this->classRoomId;
    }

    /**
     * @param string $classRoomId
     */
    public function setClassRoomId($classRoomId)
    {
        $this->classRoomId = $classRoomId;
    }

    /**
     * @return \string[]
     */
    public function getSubClassRooms()
    {
        return $this->subClassRooms;
    }

    /**
     * @param \string[] $subClassRooms
     */
    public function setSubClassRooms(array $subClassRooms)
    {
        $this->subClassRooms = $subClassRooms;
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param GroupInterface $group
     */
    public function setGroup(GroupInterface $group)
    {
        $this->group = $group;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return (!empty($this->classRoomId) && !empty($this->title));
    }

    /**
     * Helper to see if the classroom has sub classes
     * @return bool
     */
    public function hasSubClasses()
    {
        return !empty($this->subClassRooms);
    }
}
