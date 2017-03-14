<?php

namespace Application\Utils\Date;

/**
 * An Interface that allows an object to have a updated date
 * @SWG\Definition(
 *     definition="DateUpdated",
 *     description="The date this entity was last updated",
 *     @SWG\Property(
 *         type="string",
 *         format="date-time",
 *         property="updated",
 *         description="The date this was updated"
 *     )
 * )
 */
interface DateUpdatedInterface
{
    /**
     * Gets the date the object was last updated
     *
     * @return \DateTime|null
     */
    public function getUpdated();

    /**
     * Sets the date the last object was updated
     *
     * @param \DateTime|null $updated
     */
    public function setUpdated($updated);
}
