<?php

namespace Org;

use Application\Utils\Date\SoftDeleteInterface;
use Application\Utils\Date\StandardDateInterface;
use Application\Utils\Meta\MetaDataInterface;
use Application\Utils\Type\TypeInterface;
use Search\SearchableDocumentInterface;

/**
 * An Organization
 *
 * Each organization can represent a collection of Groups
 *
 * @SWG\Definition(
 *     definition="Organization",
 *     description="An Organization represents a collection of groups",
 *     required={"org_id","title","type"},
 *     x={
 *          "search-doc-id":"org_id",
 *          "search-doc-type":"org"
 *     },
 *     allOf={
 *          @SWG\Schema(ref="#/definitions/DateCreated"),
 *          @SWG\Schema(ref="#/definitions/DateUpdated"),
 *          @SWG\Schema(ref="#/definitions/DateDeleted"),
 *          @SWG\Schema(ref="#/definitions/Searchable"),
 *          @SWG\Schema(ref="#/definitions/OuType"),
 *          @SWG\Schema(ref="#/definitions/MetaData")
 *     },
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="org_id",
 *          description="The id of the organization"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="title",
 *          description="What to call this organization"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="description",
 *          description="A Description for this organization"),
 * )
 */
interface OrganizationInterface extends
    SoftDeleteInterface,
    StandardDateInterface,
    TypeInterface,
    MetaDataInterface,
    SearchableDocumentInterface
{
    /**
     * Takes an array of data to add to the object
     *
     * @param array $array
     *
     * @return OrganizationInterface
     */
    public function exchangeArray(array $array): OrganizationInterface;

    /**
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Sets the Id for the organization
     *
     * @param string $orgId
     *
     * @return OrganizationInterface
     */
    public function setOrgId(string $orgId): OrganizationInterface;

    /**
     * Gets the Organization Id
     *
     * @return string
     */
    public function getOrgId(): string;

    /**
     * Sets the Organizations title
     *
     * @param string $title
     *
     * @return OrganizationInterface
     */
    public function setTitle(string $title): OrganizationInterface;

    /**
     * Gets the title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Description for the organization
     *
     * @param string $description
     *
     * @return OrganizationInterface
     */
    public function setDescription(string $description): OrganizationInterface;

    /**
     * Returns the description of the organization
     * @return string
     */
    public function getDescription();
}
