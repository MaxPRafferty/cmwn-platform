<?php

namespace Import\Importer\Nyc\Parser;

use Group\Service\UserGroupServiceInterface;
use Import\ActionInterface;
use Import\Importer\Nyc\Teachers\Teacher;

/**
 * Class AddTeacherToGroupAction
 */
class AddTeacherToGroupAction implements ActionInterface
{
    /**
     * @var Teacher
     */
    protected $teacher;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * AddTeacherToGroupAction constructor.
     *
     * @param Teacher $teacher
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(Teacher $teacher, UserGroupServiceInterface $userGroupService)
    {
        $this->teacher          = $teacher;
        $this->userGroupService = $userGroupService;
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $group = $this->teacher->getClassRoom()->getGroup();
        $user  = $this->teacher->getUser();

        $this->userGroupService->attachUserToGroup(
            $group,
            $user,
            $this->teacher->getRole()
        );
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 10;
    }
}
