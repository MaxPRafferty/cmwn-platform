<?php

namespace User;

/**
 * Interface ChildInterface
 *
 * Defines what is needed for a child
 */
interface ChildInterface extends UserInterface
{

    /**
     * @return bool
     */
    public function isNameGenerated();

    /**
     * @return null|\stdClass
     */
    public function getGeneratedName();
}
