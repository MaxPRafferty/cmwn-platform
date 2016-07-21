<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Teachers\AddTeacherAction;
use Import\Importer\Nyc\Teachers\Teacher;
use User\Service\UserServiceInterface;

/**
 * Class TeacherParserSetup
 *
 * Helper to set up expectations for the teacher parser
 */
class TeacherParserSetup
{
    /**
     * @var \SplPriorityQueue|AddTeacherAction[]
     */
    protected $actions = [];

    /**
     * @param UserServiceInterface $userService
     * @return \Import\Importer\Nyc\Teachers\AddTeacherAction[]
     */
    public function getExpectedGoodActions(UserServiceInterface $userService)
    {
        $this->actions = new \SplPriorityQueue();
        $this->addActionForMariaSandoval($userService)
            ->addActionForCarolSmitt($userService)
            ->addActionForSamSolomon($userService)
            ->addActionForBrittneyLucas($userService)
            ->addActionForJenniferRamirez($userService)
            ->addActionForTanyaAddonis($userService)
            ->addActionForKyleAgnone($userService)
            ->addActionForAstaAgrawal($userService)
            ->addActionForEdwardLopez($userService)
            ->addActionForMaryKind($userService);

        return $this->actions;
    }

    /**
     * @param UserServiceInterface $userService
     * @return \Import\Importer\Nyc\Teachers\AddTeacherAction[]
     */
    public function getExpectedMixedActions(UserServiceInterface $userService)
    {
        $this->actions = new \SplPriorityQueue();
        $this->addActionForMariaSandoval($userService)
            ->addActionForSamSolomon($userService)
            ->addActionForBrittneyLucas($userService)
            ->addActionForJenniferRamirez($userService)
            ->addActionForTanyaAddonis($userService)
            ->addActionForKyleAgnone($userService)
            ->addActionForAstaAgrawal($userService)
            ->addActionForEdwardLopez($userService)
            ->addActionForMaryKind($userService);

        return $this->actions;
    }

    /**
     * @param UserServiceInterface $userService
     * @return \Import\Importer\Nyc\Teachers\AddTeacherAction[]
     */
    public function getExpectedWarningActions(UserServiceInterface $userService)
    {
        $this->actions = new \SplPriorityQueue();
        $this->addActionForMariaSandoval($userService)
            ->addActionForCarolSmitt($userService)
            ->addActionForSamSolomon($userService)
            ->addActionForBrittneyLucas($userService)
            ->addActionForTanyaAddonis($userService)
            ->addActionForKyleAgnone($userService)
            ->addActionForAstaAgrawal($userService)
            ->addActionForEdwardLopez($userService)
            ->addActionForMaryKind($userService);

        return $this->actions;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForMariaSandoval(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Principal')
            ->setFirstName('Maria')
            ->setLastName('Sandoval')
            ->setEmail('sandoval@gmail.com')
            ->setGender('F');

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForCarolSmitt(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Assistant Principal')
            ->setFirstName('Carol')
            ->setLastName('Smitt')
            ->setEmail('smith@gmail.com')
            ->setGender('F');

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForSamSolomon(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Sam')
            ->setLastName('Solomon')
            ->setEmail('solomon@gmail.com')
            ->setGender('M')
            ->setClassRoom(new ClassRoom('History of the world', '01X100-001'));

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForBrittneyLucas(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Brittney')
            ->setLastName('Lucas')
            ->setEmail('lucas@gmail.com')
            ->setGender('F')
            ->setClassRoom(new ClassRoom('History of the world', '01X100-001'));

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForJenniferRamirez(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Jennifer')
            ->setLastName('Ramirez')
            ->setEmail('ramirex@gmail.com')
            ->setGender('F')
            ->setClassRoom(new ClassRoom('History of the world', '01X100-001'));

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForTanyaAddonis(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Tanya')
            ->setLastName('Addonis')
            ->setEmail('addonis@gmail.com')
            ->setGender('F')
            ->setClassRoom(new ClassRoom('History of the world', '01X100-001'));

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForKyleAgnone(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Kyle')
            ->setLastName('Agnone')
            ->setEmail('agnone@gmail.com')
            ->setGender('M')
            ->setClassRoom(new ClassRoom('History of the world', '01X100-001'));

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForAstaAgrawal(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Asta')
            ->setLastName('Agrawal')
            ->setEmail('agrawal@gmail.com')
            ->setGender('M');

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForEdwardLopez(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Edward')
            ->setLastName('Lopez')
            ->setEmail('lopez@gmail.com')
            ->setGender('M');

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForMaryKind(UserServiceInterface $userService)
    {
        $teacher = new Teacher();
        $teacher->setRole('Teacher')
            ->setFirstName('Mary')
            ->setLastName('Kind')
            ->setEmail('kind@gmail.com')
            ->setGender('F');

        $action = new AddTeacherAction($userService, $teacher);
        $this->actions->insert($action, $action->priority());
        return $this;
    }
}
