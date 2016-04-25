<?php

namespace ImportTest\Importer\Nyc\Parser\Excel;

use Import\ActionInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Students\AddStudentAction;
use Import\Importer\Nyc\Students\Student;
use User\Service\UserServiceInterface;

/**
 * Class StudentParserSetup
 *
 * Helper to set up tests for the student parse
 */
class StudentParserSetup
{
    /**
     * @var \SplPriorityQueue|ActionInterface[]
     */
    protected $actions;

    /**
     * @param UserServiceInterface $userService
     * @return \SplPriorityQueue
     */
    public function getExpectedGoodActions(UserServiceInterface $userService)
    {
        $this->actions = new \SplPriorityQueue();
        $this->addActionForDylanSmith($userService)
            ->addActionForCollinMarts($userService)
            ->addActionForSallySmith($userService)
            ->addActionForSaraJohnson($userService)
            ->addActionForChristieWilliams($userService);

        return $this->actions;
    }

    /**
     * @param UserServiceInterface $userService
     * @return \SplPriorityQueue
     */
    public function getExpectedGoodActionsForBlanks(UserServiceInterface $userService)
    {
        $this->actions = new \SplPriorityQueue();
        $this->addActionForDylanSmith($userService)
            ->addActionForCollinMarts($userService)
            ->addActionForSallySmith($userService)
            ->addActionForChristieWilliams($userService);

        return $this->actions;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForDylanSmith(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Dylan')
            ->setLastName('Smith')
            ->setStudentId('123')
            ->setGender('M')
            ->setBirthday(new \DateTime('06/02/2015'))
            ->setEmail('')
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => 'Harper',
                'ADULT FIRST 1' => 'Benny',
                'ADULT PHONE 1' => '2555555555',
                'ADULT LAST 2'  => 'Lisa',
                'ADULT FIRST 2' => 'Harper',
                'ADULT PHONE 2' => '555555555',
                'ADULT LAST 3'  => 'Grandma',
                'ADULT FIRST 3' => 'Harper',
                'ADULT PHONE 3' => '555555555',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForCollinMarts(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Collin')
            ->setLastName('Marts')
            ->setStudentId('1234')
            ->setGender('M')
            ->setEmail('')
            ->setBirthday(new \DateTime('05/31/2015'))
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => 'Bob',
                'ADULT FIRST 1' => 'Happy',
                'ADULT PHONE 1' => '2777777777',
                'ADULT LAST 2'  => 'Lindsay',
                'ADULT FIRST 2' => 'Happy',
                'ADULT PHONE 2' => '777777777',
                'ADULT LAST 3'  => '',
                'ADULT FIRST 3' => '',
                'ADULT PHONE 3' => '',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForSallySmith(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Sally')
            ->setLastName('Smith')
            ->setStudentId('12345')
            ->setGender('F')
            ->setEmail('')
            ->setBirthday(new \DateTime('12/31/2014'))
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => 'Barry',
                'ADULT FIRST 1' => 'Makerday',
                'ADULT PHONE 1' => '2222222222',
                'ADULT LAST 2'  => '',
                'ADULT FIRST 2' => '',
                'ADULT PHONE 2' => '',
                'ADULT LAST 3'  => '',
                'ADULT FIRST 3' => '',
                'ADULT PHONE 3' => '',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForSaraJohnson(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Sara')
            ->setLastName('Johnson')
            ->setStudentId('123456')
            ->setGender('F')
            ->setEmail('')
            ->setBirthday(new \DateTime('11/23/2015'))
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => '',
                'ADULT FIRST 1' => '',
                'ADULT PHONE 1' => '',
                'ADULT LAST 2'  => '',
                'ADULT FIRST 2' => '',
                'ADULT PHONE 2' => '',
                'ADULT LAST 3'  => '',
                'ADULT FIRST 3' => '',
                'ADULT PHONE 3' => '',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionForChristieWilliams(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Christie')
            ->setLastName('Williams')
            ->setStudentId('1234567')
            ->setGender('F')
            ->setEmail('')
            ->setBirthday(new \DateTime('08/31/2015'))
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => '',
                'ADULT FIRST 1' => '',
                'ADULT PHONE 1' => '',
                'ADULT LAST 2'  => '',
                'ADULT FIRST 2' => '',
                'ADULT PHONE 2' => '',
                'ADULT LAST 3'  => '',
                'ADULT FIRST 3' => '',
                'ADULT PHONE 3' => '',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }

    /**
     * Template to add more in the future
     *
     * @param UserServiceInterface $userService
     * @return $this
     */
    protected function addActionTemplate(UserServiceInterface $userService)
    {
        $student = new Student();
        $student->setFirstName('Dylan')
            ->setLastName('Smith')
            ->setStudentId('123')
            ->setGender('M')
            ->setEmail('')
            ->setBirthday(new \DateTime('06/02/2015'))
            ->setClassRoom(new ClassRoom('History of the world', '001'))
            ->setExtra([
                'GRD CD'        => '',
                'GRD LVL'       => '',
                'STREET NUM'    => '',
                'STREET'        => '',
                'APT'           => '',
                'CITY'          => '',
                'ST'            => '',
                'ZIP'           => '',
                'HOME PHONE'    => '',
                'ADULT LAST 1'  => '',
                'ADULT FIRST 1' => '',
                'ADULT PHONE 1' => '',
                'ADULT LAST 2'  => '',
                'ADULT FIRST 2' => '',
                'ADULT PHONE 2' => '',
                'ADULT LAST 3'  => '',
                'ADULT FIRST 3' => '',
                'ADULT PHONE 3' => '',
                'STUDENT PHONE' => '',
                'MEAL CDE'      => '',
                'YTD ATTD PCT'  => '',
            ]);

        $action = new AddStudentAction($userService, $student);
        $this->actions->insert($action, $action->priority());
        return $this;
    }
}
