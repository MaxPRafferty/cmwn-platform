<?php

namespace ApplicationTest\Validator;

use Application\Validator\CheckIfDbRecordExists;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Phinx\Db\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

class CheckIfDbRecordExistsTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var CheckIfDbRecordExists
     */
    protected $validator;

    /**
     * @before
     */
    public function setUpValidator()
    {
        $adapter = \Mockery::mock(AdapterInterface::class);
        $this->validator = new CheckIfDbRecordExists([
            'table' => 'foo',
            'adapter' => $adapter,
            ''
        ]);
    }
}
