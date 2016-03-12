<?php

namespace Import\Importer\Nyc\Students;

use Import\Importer\Nyc\ClassRoom\ClassRoom;
use Import\Importer\Nyc\Exception\InvalidStudentException;
use User\UserInterface;

/**
 * Class Student
 */
class Student
{
    /**
     * @var string
     */
    protected $firstName = '';

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
     * @var \DateTime
     */
    protected $birthday;

    /**
     * @var string
     */
    protected $studentId;

    /**
     * @var ClassRoom
     */
    protected $classRoom;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var array
     */
    protected $extra = [];

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
        return !empty($this->firstName) &&
            !empty($this->lastName) &&
            !empty($this->birthday) &&
            !empty($this->studentId);
    }

    /**
     * @return bool
     */
    public function hasClassAssigned()
    {
        return $this->classRoom !== null;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     * @return Student
     */
    public function setBirthday(\DateTime $birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * @return string
     */
    public function getStudentId()
    {
        return $this->studentId;
    }

    /**
     * @param string $studentId
     * @return Student
     */
    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return $this
     * @throws InvalidStudentException
     */
    public function setUser(UserInterface $user)
    {
        if ($user->getType() !== UserInterface::TYPE_CHILD) {
            throw new InvalidStudentException('Only Children can be set as students');
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
        return 'student';
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     * @return Student
     */
    public function setExtra(array $extra)
    {
        $this->extra = $extra;
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
     * @return Student
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

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
     * @return Student
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
     * @return Student
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
     * @return Student
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
     * @return Student
     */
    public function setClassRoom(ClassRoom $classRoom)
    {
        $this->classRoom = $classRoom;

        return $this;
    }
}
