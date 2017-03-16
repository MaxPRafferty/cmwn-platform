<?php

namespace Application\Utils;

/**
 * Checks if a directory exists and creates it
 */
trait CreateDirectoryTrait
{
    /**
     * @param string $dirPath
     */
    public function createDirectory($dirPath)
    {
        $result = true;

        if (!file_exists($dirPath)) {
            $result = mkdir($dirPath, 0777);
        }

        if (!$result) {
            throw new \RuntimeException("cannot create directory " . $dirPath);
        }
    }
}
