<?php

namespace Import\Importer\Nyc\ClassRoom;

use Group\Group;
use Group\Service\GroupServiceInterface;
use Import\ActionInterface;
use Org\OrgAwareInterface;

/**
 * Class ClassRoomAction
 *
 * ${CARET}
 */
class AddClassRoomAction implements ActionInterface, OrgAwareInterface
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
     * @var string;
     */
    protected $orgId;

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
     * @return mixed
     */
    public function __toString()
    {
        return sprintf(
            'Creating new class room [%s] "%s"',
            $this->classRoom->getClassRoomId(),
            $this->classRoom->getTitle()
        );
    }

    /**
     * @param $orgId
     */
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;
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
        $group->setType('class');
        $group->setMeta(['sub_class_rooms' => $this->classRoom->getSubClassRooms()]);
        $group->setOrganizationId($this->orgId);
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
