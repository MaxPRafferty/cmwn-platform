<?php

namespace User;

use Application\Utils\Date\DateTimeFactory;
use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * An abstract user that satisfies some of UserInterface
 */
abstract class User implements
    ArraySerializableInterface,
    UserInterface
{
    use StandardDatesTrait,
        MetaDataTrait,
        PropertiesTrait,
        SoftDeleteTrait {
        SoftDeleteTrait::getDeleted insteadof StandardDatesTrait;
        SoftDeleteTrait::setDeleted insteadof StandardDatesTrait;
        SoftDeleteTrait::formatDeleted insteadof StandardDatesTrait;
    }

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

    /**
     * @var string
     */
    protected $externalId;

    /**
     * @return string
     */
    protected $normalizedUsername;

    /**
     * User constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->exchangeArray($options);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUserName();
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): UserInterface
    {
        $defaults = [
            'user_id'     => '',
            'email'       => '',
            'first_name'  => '',
            'middle_name' => '',
            'last_name'   => '',
            'gender'      => '',
            'birthdate'   => null,
            'meta'        => [],
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'type'        => '',
            'external_id' => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        return [
            'user_id'     => $this->getUserId(),
            'username'    => (string)$this->getUserName(),
            'email'       => $this->getEmail(),
            'first_name'  => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'last_name'   => $this->getLastName(),
            'gender'      => $this->getGender(),
            'birthdate'   => $this->getBirthdate() !== null ? $this->getBirthdate()->format("Y-m-d H:i:s") : null,
            'meta'        => $this->getMeta(),
            'created'     => $this->formatCreated("Y-m-d H:i:s"),
            'updated'     => $this->formatUpdated("Y-m-d H:i:s"),
            'deleted'     => $this->formatDeleted("Y-m-d H:i:s"),
            'type'        => $this->getType(),
            'external_id' => $this->getExternalId(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @inheritdoc
     */
    public function setExternalId(string $externalId = null): UserInterface
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): string
    {
        return (string)$this->userId;
    }

    /**
     * @inheritdoc
     */
    public function setUserId(string $userId): UserInterface
    {
        $this->userId = (string)$userId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @inheritdoc
     */
    public function setUserName(string $userName): UserInterface
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getEmail(): string
    {
        return (string)$this->email;
    }

    /**
     * @inheritdoc
     */
    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFirstName(): string
    {
        return (string)$this->firstName;
    }

    /**
     * @inheritdoc
     */
    public function setFirstName(string $firstName): UserInterface
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @inheritdoc
     */
    public function setMiddleName(string $middleName = null): UserInterface
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLastName(): string
    {
        return (string)$this->lastName;
    }

    /**
     * @inheritdoc
     */
    public function setLastName(string $lastName): UserInterface
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * @inheritdoc
     */
    public function setBirthdate($birthdate): UserInterface
    {
        $birthdate       = DateTimeFactory::factory($birthdate);
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @inheritdoc
     */
    public function setGender(string $gender): UserInterface
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Normalizes out the user name when little fingers cant type
     *
     * @param string $username
     *
     * @return string
     */
    public static function normalizeUsername(string $username = null): string
    {
        return strtolower(preg_replace('/((?![a-zA-Z0-9]+).)/', '', $username));
    }

    /**
     * Gets the normalized username
     *
     * @return string
     */
    public function getNormalizedUsername(): string
    {
        if ($this->normalizedUsername === null && $this->userName !== null) {
            $this->setNormalizedUsername(static::setNormalizedUsername($this->userName));
        }

        return (string)$this->normalizedUsername;
    }

    /**
     * Sets the normalized user name
     *
     * @param string $normalizedUsername
     *
     * @return UserInterface
     */
    public function setNormalizedUsername(string $normalizedUsername = null): UserInterface
    {
        $this->normalizedUsername = $normalizedUsername;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDocumentId(): string
    {
        return $this->getUserId();
    }

    /**
     * @inheritDoc
     */
    public function getDocumentType(): string
    {
        return 'user';
    }
}
