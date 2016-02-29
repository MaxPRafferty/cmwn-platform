<?php

namespace User;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateTimeFactory;
use Application\Utils\Date\DateUpdatedTrait;
use Application\Utils\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\SoftDeleteInterface;
use Zend\Filter\StaticFilter;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Astract Class that helps all users
 *
 * @package User
 * @property string $userId
 */
abstract class User implements ArraySerializableInterface, UserInterface, SoftDeleteInterface
{
    use DateCreatedTrait;
    use DateDeletedTrait;
    use DateUpdatedTrait;
    use MetaDataTrait;
    use PropertiesTrait;

    //  This by no means is a stab at the LGBT commumnity, currently DOE only has male and female
    const GENDER_MALE   = 'Male';
    const GENDER_FEMALE = 'Female';

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $middleName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var \DateTime|null
     */
    protected $birthdate;

    /**
     * @var string
     */
    protected $gender;

    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->exchangeArray($options);
        }
    }

    /**
     * Returns the type of user
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Converts an Array into something that can be digested here
     *
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'user_id'     => null,
            'username'    => null,
            'email'       => null,
            'first_name'  => null,
            'middle_name' => null,
            'last_name'   => null,
            'gender'      => null,
            'birthdate'   => null,
            'meta'        => [],
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'type'        => null
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'user_id'     => $this->getUserId(),
            'username'    => $this->getUserName(),
            'email'       => $this->getEmail(),
            'first_name'  => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'last_name'   => $this->getLastName(),
            'gender'      => $this->getGender(),
            'birthdate'   => $this->getBirthdate() !== null ? $this->getBirthdate()->format('Y-m-d') : null,
            'meta'        => $this->getMeta(),
            'created'     => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'     => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'     => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
            'type'        => $this->getType()
        ];
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     * @return User
     */
    public function setUserId($userId)
    {
        $this->userId = (string) $userId;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

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
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param null|string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param null|string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param null|string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime|null $birthdate
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $birthdate = DateTimeFactory::factory($birthdate);
        $this->birthdate = $birthdate;

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
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;
        return $this;
    }

}
