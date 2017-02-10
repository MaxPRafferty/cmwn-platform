<?php

namespace User;

use Application\Utils\Date\SoftDeleteInterface;
use Application\Utils\Date\StandardDateInterface;
use Application\Utils\Meta\MetaDataInterface;
use Search\SearchableDocumentInterface;

/**
 * A User is able to login to the system
 *
 * There are 2 types of users: ADULT and CHILD.  For the most part they will behave the same way.  A child will be
 * restricted to some functions (like having an email or custom username)
 *
 * @SWG\Definition(
 *     definition="User",
 *     description="A User is able to login to the system",
 *     required={"user_id","user_name","email","first_name","last_name","type"},
 *     x={
 *          "search-doc-id":"user_id",
 *          "search-doc-type":"user"
 *     },
 *     allOf={
 *          @SWG\Schema(ref="#/definitions/DateCreated"),
 *          @SWG\Schema(ref="#/definitions/DateUpdated"),
 *          @SWG\Schema(ref="#/definitions/DateDeleted"),
 *          @SWG\Schema(ref="#/definitions/Searchable"),
 *          @SWG\Schema(ref="#/definitions/MetaData")
 *     },
 *     @SWG\Property(
 *          property="type",
 *          type="string",
 *          description="The type of user",
 *          enum={"CHILD","ADULT"},
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="user_id",
 *          description="The id of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="user_name",
 *          description="A Custom name the for the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="email",
 *          property="email",
 *          description="The Email of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="first_name",
 *          description="The first name of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="middle_name",
 *          description="The middle name of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="last_name",
 *          description="The last name of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="date-time",
 *          property="birthdate",
 *          description="The users birthday"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="gender",
 *          description="The Gender the user supplied"
 *     ),
 *     @SWG\Property(
 *         type="string",
 *         property="external_id",
 *         description="An identifier of the user in a 3rd party system (like the school)"
 *     )
 * )
 */
interface UserInterface extends
    SearchableDocumentInterface,
    StandardDateInterface,
    SoftDeleteInterface,
    MetaDataInterface
{
    const TYPE_ADULT = 'ADULT';
    const TYPE_CHILD = 'CHILD';

    //  This by no means is a stab at the LGBT community, currently DOE only has male and female
    const GENDER_MALE   = 'Male';
    const GENDER_FEMALE = 'Female';

    /**
     * @param $externalId
     *
     * @return UserInterface
     */
    public function setExternalId(string $externalId = null): UserInterface;

    /**
     * @return string|null
     */
    public function getExternalId();

    /**
     * Gets the type of user
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Converts an Array into something that can be digested here
     *
     * @param array $array
     *
     * @return UserInterface
     */
    public function exchangeArray(array $array): UserInterface;

    /**
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * @return string
     */
    public function getUserId(): string;

    /**
     * @param string $userId
     *
     * @return UserInterface
     */
    public function setUserId(string $userId): UserInterface;

    /**
     * @return string|null
     */
    public function getUserName();

    /**
     * @param string $userName
     *
     * @return UserInterface
     */
    public function setUserName(string $userName): UserInterface;

    /**
     * @return string
     */
    public function getEmail(): string;

    /**
     * @param string $email
     *
     * @return UserInterface
     */
    public function setEmail(string $email): UserInterface;

    /**
     * Gets the first name of the user
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $firstName
     *
     * @return UserInterface
     */
    public function setFirstName(string $firstName): UserInterface;

    /**
     * Gets the users Middle name if supplied
     *
     * @return string|null
     */
    public function getMiddleName();

    /**
     * @param null|string $middleName
     *
     * @return UserInterface
     */
    public function setMiddleName(string $middleName = null);

    /**
     * Gets the last name of the user
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param null|string $lastName
     *
     * @return UserInterface
     */
    public function setLastName(string $lastName): UserInterface;

    /**
     * Gets the users birthday
     *
     * @return \DateTime|null
     */
    public function getBirthdate();

    /**
     * @param \DateTime|null $birthdate
     *
     * @return UserInterface
     */
    public function setBirthdate($birthdate): UserInterface;

    /**
     * Gets the gender the user supplied to the user
     *
     * @return string|null
     */
    public function getGender();

    /**
     * @param string $gender
     *
     * @return UserInterface
     */
    public function setGender(string $gender): UserInterface;
}
