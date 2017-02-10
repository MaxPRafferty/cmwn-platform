<?php

namespace SecurityTest\Authentication;

use Application\Exception\NotFoundException;
use Lcobucci\JWT\Builder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Security\Authentication\AuthAdapter;
use Security\ChangePasswordUser;
use Security\GuestUser;
use Security\SecurityUser;
use Security\Service\SecurityService;
use Security\Service\SecurityServiceInterface;
use Zend\Authentication\Result;

/**
 * Test AuthAdapterTest
 *
 * @group Security
 * @group User
 * @group Authentication
 * @group Authentication
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthAdapterTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|SecurityServiceInterface
     */
    protected $securityService;

    /**
     * @var AuthAdapter
     */
    protected $adapter;

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->adapter = new AuthAdapter($this->securityService);
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->securityService = \Mockery::mock(SecurityServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldAllowLoginWithEmail()
    {
        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'password' => SecurityService::encryptPassword('is it secret? is it safe?'),
        ]);

        $this->securityService->shouldReceive('fetchUserByEmail')
            ->once()
            ->andReturn($authUser);

        $this->securityService->shouldNotReceive('fetchUserByUserName');

        $this->adapter->setPassword('is it secret? is it safe?');
        $this->adapter->setUserIdentifier('chuck@manchuck.com');

        $this->assertEquals(
            new Result(Result::SUCCESS, $authUser),
            $this->adapter->authenticate(),
            'User was not allowed to login with email address'
        );
    }

    /**
     * @test
     */
    public function testItShouldAllowLoginWithUserName()
    {
        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'password' => SecurityService::encryptPassword('is it secret? is it safe?'),
        ]);

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->securityService->shouldNotReceive('fetchUserByEmail');

        $this->adapter->setPassword('is it secret? is it safe?');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::SUCCESS, $authUser),
            $this->adapter->authenticate(),
            'User was not allowed to login with username'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWithDeletedName()
    {
        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'password' => SecurityService::encryptPassword('is it secret? is it safe?'),
            'deleted'  => new \DateTime('1982-05-13 23:43:00'),
        ]);

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('is it secret? is it safe?');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser()),
            $this->adapter->authenticate(),
            'Deleted user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWithWrongPassword()
    {
        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'password' => SecurityService::encryptPassword('is it secret? is it safe?'),
        ]);

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('This is not the password');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::FAILURE_CREDENTIAL_INVALID, new GuestUser()),
            $this->adapter->authenticate(),
            'Wrong password user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldAllowLoginWithCodeUser()
    {

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 50)
            ->setExpiration(time() + 50)
            ->setId('nom-nom-nom')
            ->getToken();

        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ]);

        $this->assertEquals(
            SecurityUser::CODE_VALID,
            $authUser->compareCode('nom-nom-nom')
        );

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('nom-nom-nom');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::SUCCESS, new ChangePasswordUser($authUser->getArrayCopy())),
            $this->adapter->authenticate(),
            'Wrong password user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWhenCodeIsExpired()
    {

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() - 150)
            ->setExpiration(time() - 50)
            ->setId('nom-nom-nom')
            ->getToken();

        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ]);

        $this->assertEquals(
            SecurityUser::CODE_EXPIRED,
            $authUser->compareCode('nom-nom-nom')
        );

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('nom-nom-nom');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::FAILURE_UNCATEGORIZED, new GuestUser()),
            $this->adapter->authenticate(),
            'Wrong password user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWhenCodeIsOutsideWindow()
    {

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setIssuedAt(time() + 150)
            ->setExpiration(time() + 250)
            ->setId('nom-nom-nom')
            ->getToken();

        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ]);

        $this->assertEquals(
            SecurityUser::CODE_EXPIRED,
            $authUser->compareCode('nom-nom-nom')
        );

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('nom-nom-nom');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::FAILURE_UNCATEGORIZED, new GuestUser()),
            $this->adapter->authenticate(),
            'Wrong password user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWhenCodeHasNoStartDate()
    {

        $token     = (new Builder())->setAudience('abcd-efgh')
            ->setExpiration(time() + 50)
            ->setId('nom-nom-nom')
            ->getToken();

        $authUser = new SecurityUser([
            'user_id'  => 'man-chuck',
            'username' => 'manchuck',
            'email'    => 'chuck@manchuck.com',
            'code'     => $token->__toString(),
        ]);

        $this->assertEquals(
            SecurityUser::CODE_VALID,
            $authUser->compareCode('nom-nom-nom')
        );

        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andReturn($authUser);

        $this->adapter->setPassword('nom-nom-nom');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::SUCCESS, new ChangePasswordUser($authUser->getArrayCopy())),
            $this->adapter->authenticate(),
            'Wrong password user was allowed to login'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWithNonExistentUserByEmail()
    {
        $this->securityService->shouldReceive('fetchUserByEmail')
            ->once()
            ->andThrow(new NotFoundException());

        $this->securityService->shouldNotReceive('fetchUserByUserName');

        $this->adapter->setPassword('is it secret? is it safe?');
        $this->adapter->setUserIdentifier('chuck@manchuck.com');

        $this->assertEquals(
            new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser()),
            $this->adapter->authenticate(),
            'Non existent user was allowed to login with email address'
        );
    }

    /**
     * @test
     */
    public function testItShouldDenyLoginWithNonExistentUserByUserName()
    {
        $this->securityService->shouldReceive('fetchUserByUserName')
            ->once()
            ->andThrow(new NotFoundException());

        $this->securityService->shouldNotReceive('fetchUserByEmail');

        $this->adapter->setPassword('is it secret? is it safe?');
        $this->adapter->setUserIdentifier('manchuck');

        $this->assertEquals(
            new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser()),
            $this->adapter->authenticate(),
            'Non existent user was allowed to login with username'
        );
    }
}
