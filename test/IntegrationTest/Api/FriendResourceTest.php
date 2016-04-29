<?php

namespace IntegrationTest\Api;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Service\UserServiceInterface;
use User\StaticUserFactory;
use User\UserInterface;
use Zend\Json\Json;

/**
 * Test FriendResourceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FriendResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSetCorrectStatusOnEntities()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/friend');
        $this->assertResponseStatusCode(200);
    }
}
