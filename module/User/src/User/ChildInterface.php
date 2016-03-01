<?php


namespace User;


interface ChildInterface extends UserInterface
{

    /**
     * @return bool
     */
    public function isNameGenerated();

    /**
     * @return null|\stdClass
     */
    public function getGenratedName();
}
