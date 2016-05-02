<?php

namespace IntegrationTest;

use PHPUnit_Extensions_Database_TestCase as TestCase;

/**
 * Class AbstractDbTestCase
 */
abstract class AbstractDbTestCase extends TestCase
{
    use DbUnitConnectionTrait;
}
