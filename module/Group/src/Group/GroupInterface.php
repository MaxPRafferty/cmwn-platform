<?php

namespace Group;

use Org\OrganizationInterface;

/**
 * Interface GroupInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface GroupInterface
{
    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * @return string
     */
    public function getGroupId();

    /**
     * @param string $groupId
     * @return Group
     */
    public function setGroupId($groupId);

    /**
     * @return string
     */
    public function getOrganizationId();

    /**
     * @param string|OrganizationInterface $organizationId
     * @return Group
     */
    public function setOrganizationId($organizationId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return Group
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return Group
     */
    public function setDescription($description);

    /**
     * @return array
     */
    public function getMeta();

    /**
     * @param array $meta
     * @return Group
     */
    public function setMeta($meta);

    /**
     * @return int
     */
    public function getHead();

    /**
     * @param int $left
     * @return Group
     */
    public function setHead($left);

    /**
     * @return int
     */
    public function getTail();

    /**
     * @param int $right
     * @return Group
     */
    public function setTail($right);

    /**
     * @return int
     */
    public function getDepth();

    /**
     * @param int $depth
     * @return Group
     */
    public function setDepth($depth);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return Group
     */
    public function setType($type);

    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return \DateTime|null
     */
    public function getUpdated();

    /**
     * @param \DateTime|null $updated
     * @return $this
     */
    public function setUpdated($updated);

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
     * @return bool
     */
    public function isDeleted();

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
     * Gets the users Identifier for this group
     *
     * @return string
     */
    public function getExternalId();

    /**
     * Sets the users identifier for this group
     *
     * @param $externalId
     * @return string
     */
    public function setExternalId($externalId);

    /**
     * @return null|string
     */
    public function getParentId();

    /**
     * @param null|string $parentId
     */
    public function setParentId($parentId);
}
