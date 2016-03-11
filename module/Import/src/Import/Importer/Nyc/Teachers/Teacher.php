<?php

namespace Import\Importer\Nyc\Teachers;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Exception\InvalidTeacherException;
use User\UserInterface;

/**
 * Class Teacher
 */
class Teacher
{
    /**
     * @var string
     */
    protected $role = '';

    /**
     * @var string
     */
    protected $firstName = '';

    /**
     * @var string
     */
    protected $middleName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $gender = '';

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @return bool
     */
    public function isNew()
    {
        return $this->user === null;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->email) &&
            !empty($this->firstName) &&
            !empty($this->lastName);
    }

    /**
     * @return bool
     */
    public function hasClassAssigned()
    {
        return $this->classRoom !== null;
    }


    /**
     * @param UserInterface $user
     * @return $this
     * @throws InvalidTeacherException
     */
    public function setUser(UserInterface $user)
    {
        if ($user->getType() !== UserInterface::TYPE_ADULT) {
            throw new InvalidTeacherException('Only Adults can be set as teachers');
        }

        $this->user = $user;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     * @return Teacher
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Teacher
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     * @return Teacher
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return Teacher
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Teacher
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return Teacher
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return ClassRoom
     */
    public function getClassRoom()
    {
        return $this->classRoom;
    }

    /**
     * @param ClassRoom $classRoom
     * @return Teacher
     */
    public function setClassRoom(ClassRoom $classRoom)
    {
        $this->classRoom = $classRoom;

        return $this;
    }
}
