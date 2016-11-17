<?php

namespace RestoreDb;

/**
 * Class Module
 * @package RestoreDb
 */
class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
