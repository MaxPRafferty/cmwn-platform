<?php

namespace SecurityTest;

use Lcobucci\JWT\Configuration;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;

/**
 * Test SecurityUserTest
 *
 * @group Security
 * @group User
 * @group Authentication
 * @group Authorization
 */
class SecurityUserTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCompareCorrectPasswordAndSetSomeData()
    {
        $userData = [
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'password'     => password_hash('foobar', PASSWORD_DEFAULT),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals($userData['username'], $user->getUserName());
        $this->assertEquals($userData['email'], $user->getEmail());
        $this->assertEquals($userData['user_id'], $user->getUserId());
        $this->assertTrue($user->comparePassword('foobar'));
    }

    /**
     * @test
     */
    public function testItShouldPassCode()
    {
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy('abcd-efgh')
            ->issuedAt(time())
            ->expiresAt(time() + 50)
            ->identifiedBy('some_code')
            ->getToken();

        $userData = [
            'user_id'  => 'abcd-efgh',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_VALID, $user->compareCode('some_code'));
    }

    /**
     * @test
     */
    public function testItShouldFailCodeWhenPastExpiration()
    {
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy('abcd-efgh')
            ->issuedAt(time() - 180)
            ->expiresAt(time() - 50)
            ->identifiedBy('some_code')
            ->getToken();

        $userData = [
            'user_id'  => 'abcd-efgh',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_EXPIRED, $user->compareCode('some_code'));
    }

    /**
     * @test
     */
    public function testItShouldFailCodeWhenThereIsAMisMatch()
    {
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy('abcd-efgh')
            ->issuedAt(time() - 50)
            ->expiresAt(time() + 50)
            ->identifiedBy('some_code')
            ->getToken();

        $userData = [
            'user_id'  => 'abcd-efgh',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_EXPIRED, $user->compareCode('not the code'));
    }

    /**
     * @test
     */
    public function testItShouldPassCodeIfInWindow()
    {
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy('abcd-efgh')
            ->issuedAt(time() - 50)
            ->expiresAt(time() + 50)
            ->identifiedBy('some_code')
            ->getToken();

        $userData = [
            'user_id'  => 'abcd-efgh',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_VALID, $user->compareCode('some_code'));
    }

    /**
     * @test
     */
    public function testItShouldFailCodeIfNotInWindow()
    {
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy('abcd-efgh')
            ->issuedAt(time() - 100)
            ->expiresAt(time() - 50)
            ->identifiedBy('some_code')
            ->getToken();

        $userData = [
            'user_id'  => 'abcd-efgh',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_EXPIRED, $user->compareCode('some_code'));
    }
}
