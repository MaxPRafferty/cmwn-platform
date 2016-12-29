<?php

namespace SecurityTest\Authentication;

use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authentication\AuthenticationService;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\SecurityUser;
use User\Child;
use Zend\Authentication\Storage\NonPersistent;

/**
 * Test AuthenticationServiceTest
 *
 * @group Authentication
 * @group User
 * @group Security
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthenticationServiceTest extends TestCase
{
    /**
     * @var NonPersistent
     */
    protected $storage;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @before
     */
    public function setUpStorage()
    {
        $this->storage = new NonPersistent();
    }

    /**
     * @before
     */
    public function setUpAuthenticationService()
    {
        $this->authService = new AuthenticationService($this->storage);
    }

    /**
     * @test
     */
    public function testItShouldReturnCorrectIdentity()
    {
        $user = new SecurityUser();
        $this->storage->write($user);

        $this->assertSame($user, $this->authService->getIdentity());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserNeedsToChangePassword()
    {
        $user = new ChangePasswordUser();
        $this->storage->write($user);

        $this->expectException(ChangePasswordException::class);
        $this->authService->getIdentity();
    }
}
