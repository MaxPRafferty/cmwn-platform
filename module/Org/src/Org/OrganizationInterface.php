<?php

namespace Org;

/**
 * Interface OrgInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface OrganizationInterface
{
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
     * Sets the Id for the organization
     *
     * @param $string
     * @return OrganizationInterface
     */
    public function setOrgId($string);

    /**
     * Gets the Organization Id
     *
     * @return string|null
     */
    public function getOrgId();

    /**
     * Sets the Organizations title
     *
     * @param $title
     * @return OrganizationInterface
     */
    public function setTitle($title);

    /**
     * Gets the title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Description for the organization
     *
     * @param $description
     * @return OrganizationInterface
     */
    public function setDescription($description = null);

    /**
     * @return string|null
     */
    public function getDescription();

    /**
     * @return \DateTime|null
     */
    public function getCreated();

    /**
     * @param \DateTime|string|null $created
     * @return OrganizationInterface
     */
    public function setCreated($created);

    /**
     * @return \DateTime|null
     */
    public function getDeleted();

    /**
     * @param \DateTime|string|null $deleted
     * @return OrganizationInterface
     */
    public function setDeleted($deleted);

    /**
     * @return \DateTime|null
     */
    public function getUpdated();

    /**
     * @param \DateTime|null $updated
     * @return OrganizationInterface
     */
    public function setUpdated($updated);

    /**
     * An Arbitray type
     *
     * Useful for seprating out schools, musems or other types
     *
     * @return string
     */
    public function getType();

    /**
     * Sets the type of
     *
     * @param string $type
     * @return OrganizationInterface
     */
    public function setType($type);
}
