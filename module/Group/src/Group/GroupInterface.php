<?php

namespace Group;

use Application\Utils\Date\SoftDeleteInterface;
use Application\Utils\Date\StandardDateInterface;
use Application\Utils\Meta\MetaDataInterface;
use Application\Utils\Type\TypeInterface;
use Org\OrganizationInterface;
use Search\SearchableDocumentInterface;

/**
 * A Group
 *
 * A Group is a network of users within an organization.  A User is assigned to a group with a role.  Each role
 * will descend down the tree until it reaches a new role.
 *
 * @SWG\Definition(
 *     definition="Group",
 *     description="A Group is a node in an organization network that is used to define access to a system",
 *     required={"group_id","title","organization_id","type"},
 *     x={
 *          "search-doc-id":"group_id",
 *          "search-doc-type":"group"
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
 *          property="group_id",
 *          description="The id of the group"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="title",
 *          description="What to call this group"
 *     ),
 *     @SWG\Property(
 *          type="integer",
 *          format="int32",
 *          property="head",
 *          readOnly=true,
 *          description="The head value for this node"
 *     ),
 *     @SWG\Property(
 *          type="integer",
 *          format="int32",
 *          property="tail",
 *          readOnly=true,
 *          description="The tail value for this node"
 *     ),
 *     @SWG\Property(
 *          type="integer",
 *          format="int32",
 *          property="depth",
 *          readOnly=true,
 *          description="How deep this group is in the network"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="organization_id",
 *          description="The id of the organization this belongs too"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="description",
 *          description="A Description for this group"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="has_children",
 *          readOnly=true,
 *          description="Whether the group has child nodes"
 *     ),
 *     @SWG\Property(
 *         type="string",
 *         property="external_id",
 *         description="An identifier of the group in a 3rd party system (like the school)"
 *     ),
 *     @SWG\Property(
 *         type="string",
 *         format="uuid",
 *         property="parent_id",
 *         description="The parent group id"
 *     ),
 *     @SWG\Property(
 *         type="string",
 *         format="uuid",
 *         property="network_id",
 *         readOnly=true,
 *         description="The id of the network"
 *     )
 * )
 */
interface GroupInterface extends
    StandardDateInterface,
    SoftDeleteInterface,
    MetaDataInterface,
    SearchableDocumentInterface,
    TypeInterface
{
    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     * @return GroupInterface
     */
    public function exchangeArray(array $array): GroupInterface;

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Gets the group Id
     *
     * @return string
     */
    public function getGroupId(): string;

    /**
     * Sets the group Id
     *
     * @param string $groupId
     *
     * @return GroupInterface
     */
    public function setGroupId(string $groupId): GroupInterface;

    /**
     * Gets the organization this group belongs too
     *
     * @return string
     */
    public function getOrganizationId(): string;

    /**
     * @param string $organizationId
     *
     * @return GroupInterface
     */
    public function setOrganizationId(string $organizationId): GroupInterface;

    /**
     * Attaches the group to an organization
     *
     * @param OrganizationInterface $organization
     *
     * @return GroupInterface
     */
    public function attachToOrganization(OrganizationInterface $organization): GroupInterface;

    /**
     * Gets the title of the group
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Sets the title of the group
     *
     * @param string $title
     *
     * @return GroupInterface
     */
    public function setTitle(string $title): GroupInterface;

    /**
     * Sets a description of the group
     *
     * @return string
     */
    public function getDescription();

    /**
     * Gets the description of the group
     *
     * @param string $description
     *
     * @return GroupInterface
     */
    public function setDescription(string $description): GroupInterface;

    /**
     * Gets the head value for the group in the org tree
     *
     * @return int
     */
    public function getHead(): int;

    /**
     * Sets the head value for the group in the org tree
     *
     * @param int $head
     *
     * @return GroupInterface
     */
    public function setHead(int $head): GroupInterface;

    /**
     * Gets the tail value of the group in the org tree
     *
     * @return int
     */
    public function getTail(): int;

    /**
     * Sets the tail value for the group in the org tree
     *
     * @param int $right
     *
     * @return GroupInterface
     */
    public function setTail(int $right): GroupInterface;

    /**
     * Helps get how deep the group is in a tree
     *
     * @return int
     */
    public function getDepth(): int;

    /**
     * Sets how deep the group is in the tree
     *
     * @param int $depth
     *
     * @return GroupInterface
     */
    public function setDepth(int $depth): GroupInterface;

    /**
     * Checks if the group is at the top of the tree
     *
     * @return bool
     */
    public function isRoot(): bool;

    /**
     * Checks if the group has children or not
     *
     * @return bool
     */
    public function hasChildren(): bool;

    /**
     * Gets the users Identifier for this group
     *
     * @return string
     */
    public function getExternalId();

    /**
     * Sets the 3rd party identifier for this group
     *
     * @param $externalId
     *
     * @return GroupInterface
     */
    public function setExternalId(string $externalId): GroupInterface;

    /**
     * Gets the Id of the parent group
     *
     * @return null|string
     */
    public function getParentId();

    /**
     * Sets the parent group
     *
     * @param string $parentId
     *
     * @return GroupInterface
     */
    public function setParentId(string $parentId = null): GroupInterface;

    /**
     * Attaches a group to a parent
     *
     * @param GroupInterface $parent
     *
     * @return GroupInterface
     */
    public function attachToGroup(GroupInterface $parent): GroupInterface;

    /**
     * Gets the Id of the network this group belongs too
     *
     * @return string
     */
    public function getNetworkId(): string;

    /**
     * Sets the Id of the network this group belongs too
     *
     * @param $networkId
     *
     * @return GroupInterface
     */
    public function setNetworkId(string $networkId): GroupInterface;
}
