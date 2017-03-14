<?php

namespace Application\Utils\Date;

/**
 * An Interface that allows an object to have a created date
 *
 * @SWG\Definition(
 *     definition="DateCreated",
 *     description="The date this entity was created",
 *     @SWG\Property(
 *         type="string",
 *         format="date-time",
 *         property="created",
 *         description="The date this was created"
 *     )
 * )
 */
interface DateCreatedInterface
{
    /**
     * Gets the date the object was created
     *
     * @return \DateTime|null
     */
    public function getCreated();

    /**
     * Sets the date the update was created
     *
     * @param \DateTime|string|null $created
     */
    public function setCreated($created);
}
