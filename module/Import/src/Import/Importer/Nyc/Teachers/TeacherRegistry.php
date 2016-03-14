<?php

namespace Import\Importer\Nyc\Teachers;

use Application\Exception\NotFoundException;
use Import\Importer\Nyc\Exception\InvalidTeacherException;
use User\Service\UserServiceInterface;
use \ArrayObject;
use \ArrayAccess;
use \BadMethodCallException;
use \IteratorAggregate;
use \IteratorIterator;
use User\UserInterface;

/**
 * Class TeacherRegistry
 */
class TeacherRegistry implements ArrayAccess, IteratorAggregate
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var ArrayObject|Teacher[]
     */
    protected $teachers;

    /**
     * TeacherRegistry constructor.
     *
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
        $this->teachers    = new ArrayObject();
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
     * @param Teacher $teacher
     * @throws InvalidTeacherException
     */
    public function addTeacher(Teacher $teacher)
    {
        if (!$teacher->isValid()) {
            throw new InvalidTeacherException('Teacher has invalid keys');
        }

        $this->teachers->offsetSet($teacher->getEmail(), $teacher);
        if (!$teacher->isNew()) {
            return;
        }

        $user = $this->lookUpUser($teacher->getEmail());
        if ($user instanceof UserInterface) {
            $teacher->setUser($user);
        }
    }

    /**
     * @param $email
     * @return bool|\User\UserInterface
     */
    protected function lookUpUser($email)
    {
        try {
            return $this->userService->fetchUserByEmail($email);
        } catch (NotFoundException $notFound) {

        }

        return false;
    }

    /**
     * @return IteratorIterator
     */
    public function getIterator()
    {
        return new \IteratorIterator($this->teachers);
    }

    /**
     * @param UserInterface $user
     * @return Teacher
     */
    protected function getTeacherFromUser(UserInterface $user)
    {
        $teacher = new Teacher();
        $teacher->setEmail($user->getEmail())
            ->setFirstName($user->getFirstName())
            ->setMiddleName($user->getMiddleName())
            ->setLastName($user->getLastName())
            ->setGender($user->getGender())
            ->setUser($user);

        return $teacher;
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $local = $this->teachers->offsetExists($offset);

        if ($local) {
            return true;
        }

        $user = $this->lookUpUser($offset);
        if ($user === false) {
            return false;
        }

        $this->addTeacher($this->getTeacherFromUser($user));
        return true;
    }

    /**
     * @param mixed $offset
     * @return Teacher|null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->teachers->offsetGet($offset);
        }

        return null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws InvalidTeacherException
     */
    public function offsetSet($offset, $value)
    {
        $this->addTeacher($value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('Cannot unset values from the Teacher Registry');
    }
}
