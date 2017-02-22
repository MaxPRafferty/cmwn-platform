<?php

namespace UserTest;

use Application\Exception\NotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Child;
use User\Service\UserService;
use User\UpdateUsernameValidator;

/**
 * Class UpdateUsernameValidatorTest
 * @package UserTest
 */
class UpdateUsernameValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var UpdateUsernameValidator
     */
    protected $usernameValidator;

    /**
     * @var UserService|\Mockery\MockInterface
     */
    protected $userService;

    /**
     * @before
     */
    public function setUpEmailValidator()
    {
        $this->userService = \Mockery::mock('\User\Service\UserService');
        $this->usernameValidator = new UpdateUsernameValidator([], $this->userService);
    }

    /**
     * @test
     */
    public function testItShouldAllowUniqueUsername()
    {
        $this->userService
            ->shouldReceive('fetchUserByUsername')
            ->andThrow(NotFoundException::class);
        $this->assertTrue($this->usernameValidator->isValid('english_student'));
    }

    /**
     * @test
     */
    public function testItShouldAllowSameUsernameWhenUserUpdatesHisProfile()
    {
        $this->userService
            ->shouldReceive('fetchUserByUsername')
            ->andReturn(new Child(['user_id' => 'english_student' ]));
        $this->assertTrue($this->usernameValidator->isValid('english_student', ['user_id' => 'english_student' ]));
    }

    /**
     * @test
     */
    public function testItShouldNotAllowDuplicateUsernameWHileUserCreation()
    {
        $this->userService
            ->shouldReceive('fetchUserByUsername')
            ->andReturn(new Child(['user_id' => 'math_student' ]));
        $this->assertFalse($this->usernameValidator->isValid('math_student'));
    }

    /**
     * @test
     */
    public function testItShouldNotAllow()
    {
        $this->userService
            ->shouldReceive('fetchUserByUsername')
            ->andReturn(new Child(['user_id' => 'math_student' ]));
        $this->assertFalse($this->usernameValidator->isValid('math_student', ['user_id' => 'english_student' ]));
    }
}
