<?php

namespace FriendTest;

use Application\Exception\NotFoundException;
use Friend\AttachFriendValidator;
use Friend\FriendInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use User\Adult;
use User\Child;
use User\Service\UserServiceInterface;
use Zend\Hydrator\ArraySerializable;

/**
 * Test AttachFriendValidatorTest
 *
 * @package FriendTest
 * @group   Friend
 * @group   Validator
 * @group   FriendService
 */
class AttachFriendValidatorTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var AttachFriendValidator
     */
    protected $validator;

    /**
     * @var \Mockery\MockInterface|\Friend\Service\FriendService
     */
    protected $friendService;

    /**
     * @var \Mockery\MockInterface| \User\Service\UserService
     */
    protected $userService;

    /**
     * @before
     */
    public function setUpValidator()
    {
        $this->validator = new AttachFriendValidator($this->friendService, $this->userService);
    }

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = \Mockery::mock(FriendServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = \Mockery::mock(UserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldcheckIfUserIsValid()
    {
        $this->userService->shouldReceive('fetchUser')
            ->andThrow(NotFoundException::class)
            ->once();
        $this->assertFalse($this->validator->isValid('math_student', ['user_id' => 'kid']));
    }

    /**
     * test
     */
    public function testItShouldCheckIfFriendIsValid()
    {
        $this->userService->shouldReceive('fetchUser')
            ->with('kid')
            ->andReturn(new Child(['user_id' => 'kid']))
            ->once();
        $this->userService->shouldReceive('fetchUser')
            ->with('math_student')
            ->andThrow(NotFoundException::class)
            ->once();
        $this->assertFalse($this->validator->isValid('math_student', ['user_id' => 'kid']));
    }

    /**
     * @test
     */
    public function testItShouldCheckIfSameType()
    {
        $this->userService->shouldReceive('fetchUser')
            ->with('kid')
            ->andReturn(new Child(['user_id' => 'kid']))
            ->once();
        $this->userService->shouldReceive('fetchUser')
            ->with('math_student')
            ->andReturn(new Adult(['user_id' => 'math_student']))
            ->once();
        $this->assertFalse($this->validator->isValid('math_student', ['user_id' => 'kid']));
    }

    /**
     * @test
     */
    public function testItShouldCheckIfPendingFriends()
    {
        $engStudent  = new Child(['user_id' => 'english_student']);
        $mathStudent = new Child(['user_id' => 'math_student']);
        $this->userService->shouldReceive('fetchUser')
            ->with('english_student')
            ->andReturn($engStudent)
            ->once();
        $this->userService->shouldReceive('fetchUser')
            ->with('math_student')
            ->andReturn($mathStudent)
            ->once();
        $hydrator = new ArraySerializable();
        $row      = [
            'uf_user_id'    => 'math_student',
            'uf_friend_id'  => 'english_student',
            'friend_status' => FriendInterface::PENDING,
        ];
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->andReturn($hydrator->hydrate($row, new \ArrayObject()));
        $this->assertTrue($this->validator->isValid('math_student', ['user_id' => 'english_student']));
    }

    /**
     * @test
     */
    public function testItShouldCheckIfAlreadyFriends()
    {
        $engStudent  = new Child(['user_id' => 'english_student']);
        $mathStudent = new Child(['user_id' => 'math_student']);
        $this->userService->shouldReceive('fetchUser')
            ->with('english_student')
            ->andReturn($engStudent)
            ->once();
        $this->userService->shouldReceive('fetchUser')
            ->with('math_student')
            ->andReturn($mathStudent)
            ->once();
        $hydrator = new ArraySerializable();
        $row      = [
            'uf_user_id'    => 'math_student',
            'uf_friend_id'  => 'english_student',
            'friend_status' => FriendInterface::FRIEND,
        ];
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->andReturn($hydrator->hydrate($row, new \ArrayObject()));
        $this->assertFalse($this->validator->isValid('math_student', ['user_id' => 'english_student']));
    }

    /**
     * @test
     */
    public function testItShouldCheckIfNotAlreadyFriends()
    {
        $engStudent  = new Child(['user_id' => 'english_student']);
        $mathStudent = new Child(['user_id' => 'math_student']);
        $this->userService->shouldReceive('fetchUser')
            ->with('english_student')
            ->andReturn($engStudent)
            ->once();
        $this->userService->shouldReceive('fetchUser')
            ->with('math_student')
            ->andReturn($mathStudent)
            ->once();
        $this->friendService->shouldReceive('fetchFriendForUser')
            ->andThrow(NotFriendsException::class);
        $this->assertTrue($this->validator->isValid('math_student', ['user_id' => 'english_student']));
    }
}
