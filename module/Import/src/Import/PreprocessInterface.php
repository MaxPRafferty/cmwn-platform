<?php

namespace Import;

/**
 * Interface to allow pre processing a file
 *
 * @package Import
 */
interface PreprocessInterface extends ImporterInterface
{
    /**
     * PreProcess a file
     *
     * True - File passes and can be parsed
     * False - File contains user errors and will not be imported
     *
     * @return bool
     */
    public function preProcess();

    /**
     * Gets a list of errors from the preProcessor
     *
     * If the preProcessor fails, this SHOULD return a list of errors
     * that will be sent back to the user to help them fix
     *
     * @return string[]
     */
    public function getErrors();
}
