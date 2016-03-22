<?php

namespace ImportTest\Importer\Nyc\Parser;

use Import\Importer\Nyc\Parser\ClassIdValidator;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Exception ClassIdValidatorTest
 */
class ClassIdValidatorTest extends TestCase
{
    /**
     * @dataProvider validClassIdProvider
     */
    public function testItShouldValidateClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertTrue($validator->isValid($classId), 'Validator failed a valid class id: ' . $classId);
    }

    /**
     * @dataProvider validSubClassIdProvider
     */
    public function testItShouldValidateSubClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertTrue($validator->isValid($classId), 'Validator failed a valid subclass id: ' . $classId);
    }

    /**
     * @dataProvider invalidClassIdProvider
     */
    public function testItShouldFailInvalidClassId($classId)
    {
        $validator = new ClassIdValidator();
        $this->assertFalse($validator->isValid($classId), 'Validator passed an invalid class id: ' . $classId);
    }

    /**
     * @dataProvider invalidSubClassIdProvider
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
            '001' => ['001'],
            '010' => ['010'],
            '100' => ['100'],
            '999' => ['999'],
            '222' => ['222'],
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
            '8001' => ['8001'],
            '8010' => ['8010'],
            '8100' => ['8100'],
            '8999' => ['8999'],
            '8222' => ['8222'],
        ];
    }
}
