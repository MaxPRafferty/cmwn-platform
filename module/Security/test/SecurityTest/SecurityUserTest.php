<?php

namespace SecurityTest;

use Lcobucci\JWT\Builder;
use PHPUnit\Framework\TestCase;
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

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time())
            ->setExpiration(time() + 50)
            ->setId('some_code')
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

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 180)
            ->setExpiration(time() - 50)
            ->setId('some_code')
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

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 50)
            ->setExpiration(time() + 50)
            ->setId('some_code')
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

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 50)
            ->setExpiration(time() + 50)
            ->setId('some_code')
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

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 100)
            ->setExpiration(time() - 50)
            ->setId('some_code')
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
