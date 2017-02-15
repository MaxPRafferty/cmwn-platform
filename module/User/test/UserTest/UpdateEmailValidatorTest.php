<?php

namespace UserTest;

use Application\Exception\NotFoundException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use User\Child;
use User\Service\UserService;
use User\Validator\UpdateEmailValidator;

/**
 * Class UpdateEmailValidatorTest
 * @package UserTest
 */
class UpdateEmailValidatorTest extends \PHPUnit_Framework_TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \User\Validator\UpdateEmailValidator
     */
    protected $emailValidator;

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
        $this->emailValidator = new UpdateEmailValidator([], $this->userService);
    }

    /**
     * @test
     */
    public function testItShouldAllowUniqueEmail()
    {
        $this->userService
            ->shouldReceive('fetchUserByEmail')
            ->andThrow(NotFoundException::class);
        $this->assertTrue($this->emailValidator->isValid('english_student@ginasink.com'));
    }

    /**
     * @test
     */
    public function testItShouldAllowSameEmailWhenUserUpdatesHisProfile()
    {
        $this->userService
            ->shouldReceive('fetchUserByEmail')
            ->andReturn(new Child(['user_id' => 'english_student']));
        $this->assertTrue($this->emailValidator
            ->isValid(
                'english_student@ginasink.com',
                ['user_id' => 'english_student']
            ));
    }

    /**
     * @test
     */
    public function testItShouldNotAllowDuplicateEmailWhileUserCreation()
    {
        $this->userService
            ->shouldReceive('fetchUserByEmail')
            ->andReturn(new Child(['user_id' => 'english_student']));
        $this->assertFalse($this->emailValidator
            ->isValid(
                'english_student@ginasink.com'
            ));
    }

    /**
     * @test
     */
    public function testItShouldNotAllowDUplicateEmailWhileUpdatingExistingUser()
    {
        $this->userService
            ->shouldReceive('fetchUserByEmail')
            ->andReturn(new Child(['user_id' => 'english_student']));
        $this->assertFalse($this->emailValidator
            ->isValid(
                'english_student@ginasink.com',
                ['user_id' => 'math_student']
            ));
    }
}
