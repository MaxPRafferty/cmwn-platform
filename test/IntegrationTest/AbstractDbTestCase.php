<?php

namespace IntegrationTest;

use PHPUnit\DbUnit\TestCase as TestCase;

/**
 * Class AbstractDbTestCase
 */
abstract class AbstractDbTestCase extends TestCase
{
    use DbUnitConnectionTrait;
    use LoginUserTrait;
}
