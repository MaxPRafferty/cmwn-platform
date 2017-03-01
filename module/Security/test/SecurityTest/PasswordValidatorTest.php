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
        $this->assertTrue(
            $validator->isValid($password),
            PasswordValidator::class . ' Did not validate correct password: ' . $password
        );
    }

    /**
     * @dataProvider invalidPasswords
     *
     * @param $password
     *
     * @test
     */
    public function testItShouldFailsWithWeakPassword($password, $message)
    {
        $validator = new PasswordValidator($this->authService);
        $this->assertFalse(
            $validator->isValid($password),
            PasswordValidator::class . ' passed an invalid password: ' . $password
        );

        $this->assertEquals(
            $message,
            $validator->getMessages(),
            sprintf(
                '%s is expected to have message "%s" for Invalid password %s',
                PasswordValidator::class,
                current($message),
                $password
            )
        );
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
        $exception = new ChangePasswordException(new ChangePasswordUser(['code' => 'aB1234567']));
        $validator = new PasswordValidator($this->authService);

        $this->authService
            ->shouldReceive('getIdentity')
            ->andThrow($exception)
            ->once();

        $this->assertFalse($validator->isValid('aB1234567'));
    }

    /**
     * @return array
     */
    public function validPasswords()
    {
        return [
            'Upper Lower and Number'  => ['Abcdef123'],
            'With Symbol'             => ['Abcdef!23'],
            'With Special Characters' => ['Ab3!@#$%^&*()'],
            'Real Case'               => ['JUVD2106!!'],
            'All Upper'               => ['ABCDEF123'],
            'Mixed Case'              => ['a1234567sdDSVWE'],
            'Many Special'            => ['Ab3!@#$%^&*()'],
            'German Bread'            => ['DerBrötIstGroß1'],
            'German Apple'            => ['Ichhabe1Äpfel'],
            'Spanish'                 => ['Actuación2'],
            'More Spanish '           => ['Animación¡1'],
            'Spanish Question'        => ['¿Pudiñvón1?'],
            'More Specials'           => ['¢¡™£∞§¶•ªº––≠ªº•§•¶1aA'],
            'XKCD'                    => ['correct horse Batt3ry staple'],
            'Specials (extended)'     => ['¢¡™£∞§¶•ªº––≠ªº•§•¶ab1c'],
            'No Upper With Special'   => ['abcd1$%##@®'],
        ];
    }

    /**
     * @return array
     */
    public function invalidPasswords()
    {
        return [
            'Not Long enough'          => [
                'password' => 'aA12345',
                'message'  => ['toShort' => 'Password must be at least 8 characters'],
            ],
            'Just Specials (standard)' => [
                'password' => '!@#$%^&*()',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'No Numbers'               => [
                'password' => 'abcdEF$%##@®',
                'message'  => ['noNumber' => 'You must have at least one number'],
            ],
            'One Letter rest numbers'  => [
                'password' => '12345678',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'Just Words'               => [
                'password' => 'correct horse battery staple',
                'message'  => ['noNumber' => 'You must have at least one number'],
            ],
            'Spanish Question'         => [
                'password' => '¿Pudiñvón?',
                'message'  => ['noNumber' => 'You must have at least one number'],
            ],
            'German Bread'             => [
                'password' => 'DerBrötIstGroß',
                'message'  => ['noNumber' => 'You must have at least one number'],
            ],
            'Chinese'                  => [
                'password' => '对所有帐户都要使用优良和强壮的密码零',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'Chuck with 42 Telugu'     => [
                'password' => 'చక్౪౨రీవ్స్౪౨',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'India Telugu'             => [
                'password' => 'ఇండియా౨౨౨',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'Hello 1234 Arabic'        => [
                'password' => 'مرحبا 1234',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'Greetings in Russian'     => [
                'password' => 'Приветствую',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
            'Greek'                    => [
                'password' => 'Χαιρετίσματα',
                'message'  => ['noLetter' => 'You must have at least one letter'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function passEqualsCode()
    {
        return [
            ['Foobar123'],
            ['fOoBar123'],
            ['FooBar123'],
            ['FOOBar123'],
        ];
    }
}
