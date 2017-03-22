<?php

namespace Application\Utils\Sort;

/**
 * A Trait that helps satisfy SortableInterface
 */
trait SortableTrait
{
    /**
     * @var int
     */
    protected $sortOrder = 0;

    /**
     * Gets the sort order
     *
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * Sets the sort order
     *
     * @param int $order
     *
     * @return SortableInterface
     */
    public function setSortOrder(int $order): SortableInterface
    {
        $this->sortOrder = $order;

        return $this;
    }
}
