<?php

namespace Import\Importer\Nyc\Students;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\Exception\InvalidStudentException;
use User\Service\UserServiceInterface;
use \ArrayObject;
use \ArrayAccess;
use \BadMethodCallException;
use \IteratorAggregate;
use \IteratorIterator;
use User\UserInterface;

/**
 * Class StudentRegistry
 *
 * @todo add org Id and attach to students id
 */
class StudentRegistry implements ArrayAccess, IteratorAggregate
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var ArrayObject|Student[]
     */
    protected $students;

    /**
     * StudentRegistry constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
        $this->students    = new ArrayObject();
    }

    /**
     * @return UserServiceInterface
     * @codeCoverageIgnore
     */
    public function getUserService()
    {
        return $this->userService;
    }

    /**
     * @param Student $student
     * @throws InvalidStudentException
     */
    public function addStudent(Student $student)
    {
        if (!$student->isValid()) {
            throw new InvalidStudentException('Student has invalid keys');
        }

        $this->students->offsetSet($student->getStudentId(), $student);
        if (!$student->isNew()) {
            return;
        }

        $user = $this->lookUpUser($student->getStudentId());
        if ($user instanceof UserInterface) {
            $student->setUser($user);
        }
    }

    /**
     * @param $externalId
     * @return bool|\User\UserInterface
     */
    protected function lookUpUser($externalId)
    {
        try {
            return $this->userService->fetchUserByExternalId($externalId);
        } catch (NotFoundException $notFound) {

        }

        return false;
    }

    /**
     * @return IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator($this->students);
    }

    /**
     * @param UserInterface $user
     * @return Student
     */
    protected function getStudentFromUser(UserInterface $user)
    {
        $student = new Student();
        $student->setEmail($user->getEmail())
            ->setStudentId($user->getExternalId())
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setGender($user->getGender())
            ->setBirthday($user->getBirthdate())
            ->setExtra($user->getMeta())
            ->setUser($user);

        return $student;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $local = $this->students->offsetExists($offset);

        if ($local) {
            return true;
        }

        $user = $this->lookUpUser($offset);
        if ($user === false) {
            return false;
        }

        $this->addStudent($this->getStudentFromUser($user));
        return true;
    }

    /**
     * Checks if the student exists locally
     *
     * This is used to check for when adding two students with the same Id in the same sheet
     *
     * @param $offset
     * @return bool
     */
    public function localExists($offset)
    {
        return $this->students->offsetExists((string) $offset);
    }

    /**
     * @param mixed $offset
     * @return Student|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->students->offsetGet($offset);
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws InvalidStudentException
     */
    public function offsetSet($offset, $value)
    {
        $this->addStudent($value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset values from the Student Registry');
    }
}
