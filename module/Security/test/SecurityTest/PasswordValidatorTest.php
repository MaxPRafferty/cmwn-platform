<?php

namespace SecurityTest;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\PasswordValidator;
use Security\SecurityUser;
use Zend\Authentication\AuthenticationServiceInterface;

/**
 * Test PasswordValidatorTest
 *
 * @group Security
 * @group Validator
 * @group Authentication
 */
class PasswordValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|\Zend\Authentication\AuthenticationServiceInterface
     */
    protected $authService;

    /**
     * @before
     */
    public function setUpAuthService()
    {
        $this->authService = \Mockery::mock(AuthenticationServiceInterface::class);
        $this->authService->shouldReceive('getIdentity')->andReturn(null)->byDefault();
        $this->authService->shouldReceive('hasIdentity')->andReturn(false)->byDefault();
    }

    /**
     * @dataProvider validPasswords
     *
     * @param $password
     *
     * @test
     */
    public function testItShouldPassWithStrongPassword($password)
    {
        $validator = new PasswordValidator($this->authService);
        $this->assertTrue($validator->isValid($password));
    }

    /**
     * @dataProvider invalidPasswords
     *
     * @param $password
     *
     * @test
     */
    public function testItShouldFailsWithWeakPassword($password)
    {
        $validator = new PasswordValidator($this->authService);
        $this->assertFalse($validator->isValid($password));
    }

    /**
     * @dataProvider validPasswords
     *
     * @param $password
     *
     * @ticket       CORE-732
     * @test
     */
    public function testItShouldValidateTrueWhenNewPasswordDoesNotEqualCode($password)
    {
        $securityUser = new SecurityUser(['code' => 'foobar123']);

        $validator = new PasswordValidator($this->authService);

        $this->authService->shouldReceive('getIdentity')->andReturn($securityUser)->once();
        $this->assertTrue($validator->isValid($password));
    }

    /**
     * @dataProvider validPasswords
     *
     * @param $password
     *
     * @test
     */
    public function testItShouldValidateTrueWhenUserNeedsToChangePassword($password)
    {

        $validator = new PasswordValidator($this->authService);

        $this->authService
            ->shouldReceive('getIdentity')
            ->andThrow(new ChangePasswordException(new ChangePasswordUser(['code' => 'foobar'])))
            ->once();

        $this->assertTrue($validator->isValid($password));
    }

    /**
     * @dataProvider passEqualsCode
     * @ticket       CORE-732
     * @test
     */
    public function testItShouldValidateFalseCodeMatchesNewPassword($password)
    {
        $validator = new PasswordValidator($this->authService);

        $this->authService
            ->shouldReceive('getIdentity')
            ->andReturn(new SecurityUser(['code' => 'foobar123']))
            ->once();

        $this->assertFalse($validator->isValid($password));
    }

    /**
     * @test
     */
    public function testItShouldValidateFalseCodeMatchesNewPasswordOnChangePasswordUser()
    {
        $validator = new PasswordValidator($this->authService);

        $this->authService
            ->shouldReceive('getIdentity')
            ->andThrow(new ChangePasswordException(new ChangePasswordUser(['code' => 'a1234567'])))
            ->once();

        $this->assertFalse($validator->isValid('a1234567'));
    }

    /**
     * @return array
     */
    public function validPasswords()
    {
        return [
            ['a1234567'],
            ['a1234567sdblkjeier'],
            ['a1234567sdDSVWE'],
        ];
    }

    /**
     * @return array
     */
    public function invalidPasswords()
    {
        return [
            ['a123456'],
            ['1234567'],
            ['abcd$%##@®'],
        ];
    }

    /**
     * @return array
     */
    public function passEqualsCode()
    {
        return [
            ['foobar123'],
            ['fOoBar123'],
            ['FooBar123'],
            ['FOOBar123'],
        ];
    }
}
