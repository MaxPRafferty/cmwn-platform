<?php

namespace SecurityTest\Authentication;

use Application\Exception\NotFoundException;
use \PHPUnit_Framework_TestCase as TestCase;
use Security\Authentication\CmwnAuthenticationAdapter;
use Security\ChangePasswordUser;
use Security\GuestUser;
use Security\SecurityUser;
use Zend\Authentication\Result;

class CmwnAuthenticationAdapterTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|\Security\Service\SecurityService
     */
    protected $service;

    /**
     * @var CmwnAuthenticationAdapter
     */
    protected $adapter;

    /**
     * @var 
     */
    protected $securityUser;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->service = \Mockery::mock('\Security\Service\SecurityService');
    }

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->adapter = new CmwnAuthenticationAdapter($this->service);
    }

    /**
     * @before
     */
    public function setUpSecurityUser()
    {
        $date     = new \DateTime("tomorrow");
        $this->securityUser = new SecurityUser([
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->getTimestamp(),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ]);
    }
    
    public function testItShouldThrowRuntimeExceptionWhenUserIdIsNotSet()
    {
        $this->setExpectedException(
            'Zend\Authentication\Exception\RuntimeException',
            'a User Identifier is needed in order to authenticate'
        );

        $this->adapter->authenticate();
    }

    public function testItShouldAuthenticationUsingEmail()
    {
        $this->service->shouldReceive('fetchUserByEmail')
            ->with('chuck@manchuck.com')
            ->andReturn($this->securityUser);

        $this->adapter->setPassword('foobar');
        $this->adapter->setUserIdentifier('chuck@manchuck.com');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::SUCCESS ,$result->getCode());
        $this->assertEquals($this->securityUser, $result->getIdentity());
    }

    public function testItShouldAuthenticationUsingUserName()
    {
        $this->service->shouldReceive('fetchUserByUserName')
            ->with('manchuck')
            ->andReturn($this->securityUser);

        $this->adapter->setPassword('foobar');
        $this->adapter->setUserIdentifier('manchuck');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::SUCCESS ,$result->getCode());
        $this->assertEquals($this->securityUser, $result->getIdentity());
    }

    public function testItShouldFailWhenUserNotFoundByEmail()
    {
        $this->service->shouldReceive('fetchUserByEmail')
            ->with('chuck@manchuck.com')
            ->andThrow(new NotFoundException());

        $this->adapter->setPassword('foobar');
        $this->adapter->setUserIdentifier('chuck@manchuck.com');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertEquals(new GuestUser(), $result->getIdentity());
    }

    public function testItShouldFailWhenUserNotFoundByUserName()
    {
        $this->service->shouldReceive('fetchUserByUserName')
            ->with('manchuck')
            ->andThrow(new NotFoundException());

        $this->adapter->setPassword('foobar');
        $this->adapter->setUserIdentifier('manchuck');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::FAILURE_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertEquals(new GuestUser(), $result->getIdentity());
    }

    public function testItShouldFailCodeDoesNotMatch()
    {
        $this->service->shouldReceive('fetchUserByUserName')
            ->with('manchuck')
            ->andReturn($this->securityUser);

        $this->adapter->setPassword('not password or code');
        $this->adapter->setUserIdentifier('manchuck');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::FAILURE_CREDENTIAL_INVALID, $result->getCode());
        $this->assertEquals(new GuestUser(), $result->getIdentity());
    }

    public function testItShouldFailCodeIsExpired()
    {
        $date = new \DateTime("yesterday");
        $user = new SecurityUser([
            'user_id'      => 'abcd-efgh',
            'username'     => 'manchuck',
            'email'        => 'chuck@manchuck.com',
            'code'         => 'some_code',
            'code_expires' => $date->getTimestamp(),
            'password'     => password_hash('foobar', PASSWORD_DEFAULT)
        ]);

        $this->service->shouldReceive('fetchUserByUserName')
            ->with('manchuck')
            ->andReturn($user);

        $this->adapter->setPassword('some_code');
        $this->adapter->setUserIdentifier('manchuck');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::FAILURE_UNCATEGORIZED, $result->getCode());
        $this->assertEquals(new GuestUser(), $result->getIdentity());
    }
    
    public function testItShouldReturnChangePasswordUser()
    {
        $this->service->shouldReceive('fetchUserByUserName')
            ->with('manchuck')
            ->andReturn($this->securityUser);

        $this->adapter->setPassword('some_code');
        $this->adapter->setUserIdentifier('manchuck');

        $result = $this->adapter->authenticate();

        $this->assertEquals(Result::SUCCESS, $result->getCode());
        $this->assertEquals(
            new ChangePasswordUser([
                'user_id'  => $this->securityUser->getUserId(),
                'email'    => $this->securityUser->getEmail(),
                'username' => $this->securityUser->getUserName()
            ]),
            $result->getIdentity()
        );
    }
}
