<?php

namespace Import;

/**
 * Interface to allow pre processing a file
 *
 * @package Import
 */
interface ParserInterface
{
    /**
     * PreProcess a file
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

    /**
     * Called after preProcess to check to see if any errors came up during processing
     *
     * @return bool
     */
    public function hasErrors();

    /**
     * If there are warnings that did not prevent the parser from running but you will still like
     * to notify the user.
     *
     * @return string[]
     */
    public function getWarnings();

    /**
     * Called after preProcess checks if the parser has errors or not
     *
     * @return mixed
     */
    public function hasWarnings();
}
