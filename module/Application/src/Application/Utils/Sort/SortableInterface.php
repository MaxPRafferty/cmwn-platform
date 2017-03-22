<?php

namespace Application\Utils\Sort;

/**
 * An interface that allows objects to be stored with a sort order
 */
interface SortableInterface
{
    /**
     * Gets the sort order
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Sets the sort order
     *
     * @param int $order
     *
     * @return SortableInterface
     */
    public function setSortOrder(int $order): SortableInterface;
}
