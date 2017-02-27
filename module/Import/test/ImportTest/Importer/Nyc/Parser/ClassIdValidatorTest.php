<?php

namespace ImportTest\Importer\Nyc\Parser;

use Import\Importer\Nyc\Parser\ClassIdValidator;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Test ClassIdValidatorTest
 *
 * @group Validator
 * @group ClassRoom
 * @group Group
 */
class ClassIdValidatorTest extends TestCase
{
    /**
     * @dataProvider validClassIdProvider
     * @param $classId
     * @test
     */
    public function testItShouldValidateClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertTrue($validator->isValid($classId), 'Validator failed a valid class id: ' . $classId);
    }

    /**
     * @dataProvider validSubClassIdProvider
     * @param $classId
     * @test
     */
    public function testItShouldValidateSubClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertTrue($validator->isValid($classId), 'Validator failed a valid subclass id: ' . $classId);
    }

    /**
     * @dataProvider invalidClassIdProvider
     * @param $classId
     * @test
     */
    public function testItShouldFailInvalidClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertFalse($validator->isValid($classId), 'Validator passed an invalid class id: ' . $classId);
    }

    /**
     * @dataProvider invalidSubClassIdProvider
     * @param $classId
     * @test
     */
    public function testItShouldFailInvalidSubClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertFalse($validator->isValid($classId), 'Validator passed an invalid subclass id: ' . $classId);
    }

    /**
     * @return array
     */
    public function validClassIdProvider()
    {
        return [
            '001' => ['01X001-001'],
            '010' => ['01X001-010'],
            '100' => ['01X001-100'],
            '999' => ['01X001-999'],
            '222' => ['01X001-222'],
        ];
    }

    /**
     * @return array
     */
    public function invalidClassIdProvider()
    {
        return [
            '01'   => ['01'],
            '1'    => ['1'],
            'abc'  => ['abc'],
            '1000' => ['1000'],
            '1ab'  => ['1ab'],
        ];
    }

    /**
     * @return array
     */
    public function invalidSubClassIdProvider()
    {
        return [
            '801'   => ['801'],
            '81'    => ['81'],
            '8abc'  => ['8abc'],
            '81000' => ['81000'],
            '81ab'  => ['81ab'],
        ];
    }

    /**
     * @return array
     */
    public function validSubClassIdProvider()
    {
        return [
            '8001' => ['01X001-8001'],
            '8010' => ['01X001-8010'],
            '8100' => ['01X001-8100'],
            '8999' => ['01X001-8999'],
            '8222' => ['01X001-8222'],
        ];
    }
}
