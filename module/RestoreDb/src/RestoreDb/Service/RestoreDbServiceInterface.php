<?php

namespace RestoreDb\Service;

/**
 * Interface RestoreDbServiceInterface
 * @package RestoreDb
 */
interface RestoreDbServiceInterface
{
    /**
     * Runs the seeder to update the database with default values
     * @return bool
     */
    public function runDbStateRestorer();
}
