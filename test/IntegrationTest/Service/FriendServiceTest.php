<?php

namespace IntegrationTest\Service;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test FriendServiceTest
 *
 * @group Friend
 * @group IntegrationTest
 * @group FriendService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FriendServiceTest extends TestCase
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;
    
    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = TestHelper::getServiceManager()->get(FriendServiceInterface::class);
    }
    
    /**
     * @test
     */
    public function testItShould()
    {
        $this->friendService->fetchFriendForUser('english_student', 'math_student');
    }
}
