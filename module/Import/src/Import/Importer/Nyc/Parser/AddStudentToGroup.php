<?php

namespace Import\Importer\Nyc\Parser;

use Group\Service\UserGroupServiceInterface;
use Import\ActionInterface;
use Import\Importer\Nyc\Students\Student;

/**
 * Class AddStudentToGroup
 *
 * ${CARET}
 */
class AddStudentToGroup implements ActionInterface
{
    /**
     * @var Student
     */
    protected $student;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    public function __construct(Student $student, UserGroupServiceInterface $userGroupService)
    {
        $this->student          = $student;
        $this->userGroupService = $userGroupService;
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $group = $this->student->getClassRoom()->getGroup();
        $user  = $this->student->getUser();

        $this->userGroupService->attachUserToGroup(
            $group,
            $user,
            'student'
        );
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 5;
    }
}
