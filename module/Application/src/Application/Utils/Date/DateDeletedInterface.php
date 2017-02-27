<?php

namespace Application\Utils\Date;

/**
 * An interface that allows an entity to have a deleted date
 * @SWG\Definition(
 *     definition="DateDeleted",
 *     description="The date this entity was deleted",
 *     @SWG\Property(
 *         type="string",
 *         format="date-time",
 *         property="deleted",
 *         description="The date this was deleted"
 *     )
 * )
 */
interface DateDeletedInterface
{
    /**
     * Gets the deleted date
     *
     * @return \DateTime|null
     */
    public function getDeleted();

    /**
     * Sets the date deleted
     *
     * @param \DateTime|string|null $deleted
     */
    public function setDeleted($deleted);
}
