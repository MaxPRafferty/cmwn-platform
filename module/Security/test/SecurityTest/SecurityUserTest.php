<?php

namespace SecurityTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\SecurityUser;
use User\UserInterface;

class SecurityUserTest extends TestCase
{

    public function testItShouldCompareCorrectPasswordAndSetSomeData()
    {
        $date     = new \DateTime();
        $userData = [
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->format("Y-m-d H:i:s"),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ];

        $user = new SecurityUser($userData);

        $this->assertEquals($userData['username'], $user->getUserName());
        $this->assertEquals($userData['email'], $user->getEmail());
        $this->assertEquals($userData['code'], $user->getCode());
        $this->assertEquals($userData['user_id'], $user->getUserId());
        $this->assertTrue($user->comparePassword('foobar'));
    }

    public function testItShouldFailCodeWhenPastExpiration()
    {
        $date     = new \DateTime("yesterday");
        $userData = [
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->format("Y-m-d H:i:s"),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_EXPIRED, $user->compareCode('some_code'));
    }

    public function testItShouldFailCodeWhenThereIsAMisMatch()
    {
        $date     = new \DateTime("yesterday");
        $userData = [
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->format("Y-m-d H:i:s"),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_INVALID, $user->compareCode('not the code'));
    }

    public function testItShouldPassCode()
    {
        $date     = new \DateTime("tomorrow");
        $userData = [
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->format("Y-m-d H:i:s"),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ];

        $user = new SecurityUser($userData);
        $this->assertEquals(SecurityUser::CODE_VALID, $user->compareCode('some_code'));
    }

    public function testItShouldReturnChildForRoleAllTheTimeWhenUserIsChild()
    {
        $user = new SecurityUser();
        $user->setType(UserInterface::TYPE_CHILD);

        $this->assertEquals(
            'child',
            $user->getRole(),
            'Security user did not return "child" for role when user is a child'
        );

        $user->setRole('admin');

        $this->assertEquals(
            'child',
            $user->getRole(),
            'Security user did not return "child" for role when role is set'
        );

    }
}

