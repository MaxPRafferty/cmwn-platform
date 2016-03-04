<?php

namespace Import;

/**
 * Interface To Import a file
 *
 * @package Import
 */
interface ImporterInterface
{
    /**
     * Sets the name of the file to import
     *
     * @param $file
     * @return $this
     */
    public function setFile($file);

    /**
     * Weather this importer can handle the file
     *
     * @return bool
     */
    public function canImport();

    /**
     * Run the importer
     *
     * @return bool
     */
    public function import();
}
