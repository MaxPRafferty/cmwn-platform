<?php

namespace User;

interface UserInterface
{
    const TYPE_ADULT = 'ADULT';
    const TYPE_CHILD = 'CHILD';

    /**
     * Gets the type of user
     *
     * @return string
     */
    public function getType();

    /**
     * Converts an Array into something that can be digested here
     *
     * @param array $array
     */
    public function exchangeArray(array $array);

    /**
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * @return string
     */
    public function getUserId();

    /**
     * @param string $userId
     * @return User
     */
    public function setUserId($userId);

    /**
     * @return string
     */
    public function getUserName();

    /**
     * @param string $userName
     * @return User
     */
    public function setUserName($userName);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email);

    /**
     * @return null|string
     */
    public function getFirstName();

    /**
     * @param null|string $firstName
     * @return User
     */
    public function setFirstName($firstName);

    /**
     * @return null|string
     */
    public function getMiddleName();

    /**
     * @param null|string $middleName
     * @return User
     */
    public function setMiddleName($middleName);

    /**
     * @return null|string
     */
    public function getLastName();

    /**
     * @param null|string $lastName
     * @return User
     */
    public function setLastName($lastName);

    /**
     * @return \DateTime|null
     */
    public function getBirthdate();

    /**
     * @param \DateTime|null $birthdate
     * @return User
     */
    public function setBirthdate($birthdate);

    /**
     * @return string
     */
    public function getGender();

    /**
     * @param string $gender
     * @return User
     */
    public function setGender($gender);

    /**
     * @return \DateTime|null
     */
    public function getCreated();

    /**
     * @param \DateTime|string|null $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * @return \DateTime|null
     */
    public function getDeleted();

    /**
     * @param \DateTime|string|null $deleted
     * @return $this
     */
    public function setDeleted($deleted);

    /**
     * @return \DateTime|null
     */
    public function getUpdated();

    /**
     * @param \DateTime|null $updated
     * @return $this
     */
    public function setUpdated($updated);
}
