<?php

namespace Import\Importer\Nyc\Students;

use Import\ActionInterface;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Class AddStudentAction
 *
 * ${CARET}
 */
class AddStudentAction implements ActionInterface
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var Student
     */
    protected $student;

    /**
     * AddStudentAction constructor.
     *
     * @param UserServiceInterface $userService
     * @param Student $student
     */
    public function __construct(UserServiceInterface $userService, Student $student)
    {
        $this->userService = $userService;
        $this->student     = $student;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return sprintf(
            'Creating user for a student with id %s',
            $this->student->getStudentId()
        );
    }

    /**
     * Process the action
     *
     * @return void
     */
    public function execute()
    {
        $user = new Child();
        $user->setFirstName($this->student->getFirstName());
        $user->setLastName($this->student->getLastName());
        $user->setGender($this->student->getGender());
        $user->setEmail($this->student->getEmail());
        $user->setBirthdate($this->student->getBirthday());
        $user->setExternalId($this->student->getStudentId());
        $user->setMeta($this->student->getExtra());

        $this->userService->createUser($user);
        $this->student->setUser($user);
    }

    /**
     * The priority that the action should be processed in
     *
     * @return int
     */
    public function priority()
    {
        return 20;
    }
}
