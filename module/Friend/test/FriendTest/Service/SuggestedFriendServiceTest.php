<?php

namespace FriendTest\Service;

use \PHPUnit_Framework_TestCase as TestCase;
use Application\Utils\ServiceTrait;
use Friend\Service\SuggestedFriendService;
use User\Child;
use User\UserInterface;

/**
 * Test SuggestedFriendServiceTest
 * @group Db
 * @group Service
 * @group Friend
 * @group SuggestedFriend
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) 
 */
class SuggestedFriendServiceTest extends TestCase
{
    use ServiceTrait;

    /**
     * @var SuggestedFriendService
     */
    protected $suggestedFriendService;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var \Mockery\MockInterface|\Zend\Db\Adapter\Adapter
     */
    protected $dbAdapter;

    /**
     * @before
     */
    public function setUpAdapter()
    {
        $this->dbAdapter = \Mockery::mock('Zend\Db\Adapter\Adapter');
        $this->dbAdapter->shouldReceive('getPlatform')->byDefault();
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->suggestedFriendService = new SuggestedFriendService($this->dbAdapter);
    }

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'user']);
    }

    /**
     * @test
     */
    public function testItShouldSuggestFriends()
    {
        $result = $this->suggestedFriendService->fetchSuggestedFriends($this->user);
        $this->assertInstanceOf(
            'Zend\Paginator\Adapter\DbSelect',
            $result,
            'Suggested Friend Service did not return Paginator adapter'
        );
    }
}
