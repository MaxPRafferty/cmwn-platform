<?php

namespace Import\Importer;

use Import\PreprocessInterface;

/**
 * Class NycDoeImporter
 *
 * @package Import\Importer
 */
class NycDoeImporter implements PreprocessInterface
{
    /**
     * Sets the name of the file to import
     *
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        // TODO: Implement setFile() method.
    }

    /**
     * Weather this importer can handle the file
     *
     * @return bool
     */
    public function canImport()
    {
        // TODO: Implement canImport() method.
    }

    /**
     * Run the importer
     *
     * @return bool
     */
    public function import()
    {
        // TODO: Implement import() method.
    }

    /**
     * PreProcess a file
     *
     * True - File passes and can be parsed
     * False - File contains user errors and will not be imported
     *
     * @return bool
     */
    public function preProcess()
    {
        // TODO: Implement preProcess() method.
    }

    /**
     * Gets a list of errors from the preProcessor
     *
     * If the preProcessor fails, this SHOULD return a list of errors
     * that will be sent back to the user to help them fix
     *
     * @return string[]
     */
    public function getErrors()
    {
        return [];
    }
}
