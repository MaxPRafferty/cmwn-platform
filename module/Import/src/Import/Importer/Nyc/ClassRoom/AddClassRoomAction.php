<?php

namespace Import\Importer\Nyc\ClassRoom;

use Group\Group;
use Group\Service\GroupServiceInterface;
use Import\ActionInterface;

/**
 * Class ClassRoomAction
 *
 * ${CARET}
 */
class AddClassRoomAction implements ActionInterface
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * ClassRoomAction constructor.
     * @param GroupServiceInterface $groupService
     * @param ClassRoom $classRoom
     */
    public function __construct(GroupServiceInterface $groupService, ClassRoom $classRoom)
    {
        $this->groupService = $groupService;
        $this->classRoom    = $classRoom;
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $group = new Group();
        $group->setExternalId($this->classRoom->getClassRoomId());
        $group->setTitle($this->classRoom->getTitle());
        $group->setMeta(['sub_class_rooms' => $this->classRoom->getSubClassRooms()]);

        $this->groupService->saveGroup($group);
        $this->classRoom->setGroup($group);
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 100;
    }
}
