<?php

namespace GroupTest;

use Group\RoleValidator;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Class RoleValidatorTest
 * @package GroupTest
 */
class RoleValidatorTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var RoleValidator
     */
    protected $validator;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userService = \Mockery::mock(UserServiceInterface::class);
        $this->validator = new RoleValidator(
            $this->userService,
            [
                'cmwn-roles' => [
                    'roles' => [
                        'student.child' => [],
                        'teacher.adult' => [],
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnTrueForValidRole()
    {
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('es')
            ->andReturn(new Child(['user_id' => 'es']));
        $this->assertTrue($this->validator->isValid('student', ['user_id' => 'es']));
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseForUnauthorizedRole()
    {
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('es')
            ->andReturn(new Child(['user_id' => 'es']));
        $this->assertFalse($this->validator->isValid('teacher', ['user_id' => 'es']));
    }

    /**
     * @test
     */
    public function testItShouldReturnFalseForInexistentRole()
    {
        $this->userService
            ->shouldReceive('fetchUser')
            ->with('es')
            ->andReturn(new Child(['user_id' => 'es']));
        $this->assertFalse($this->validator->isValid('foo', ['user_id' => 'es']));
    }
}
