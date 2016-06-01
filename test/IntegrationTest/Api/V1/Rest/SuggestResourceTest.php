<?php

namespace IntegrationTest\Api\V1\Rest;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Test FriendResourceTest
 *
 * @group Friend
 * @group IntegrationTest
 * @group FriendService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SuggestResourceTest extends TestCase
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        if (static::$dataSet === null) {
            $data = include __DIR__ . '/../../../DataSets/friends.dataset.php';
            static::$dataSet = new ArrayDataSet($data);
        }

        return static::$dataSet;
    }

    /**
     * @before
     */
    public function setUpFriendService()
    {
        $this->friendService = TestHelper::getServiceManager()->get(FriendServiceInterface::class);
    }

    /**
     * @test
     * @ticket CORE-558
     */
    public function testItShouldAllowChildToAccessSuggestEndpoint()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');
    }
 
    /**
     * @test
     * @ticket CORE-701
     */
    public function testItShouldReturnSuggestionsWhenUserHasPendingRequest()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->friendService->attachFriendToUser('english_student', 'math_student');
        $this->dispatch('/user/english_student/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $this->assertEquals(2, count($body['_embedded']['suggest']));
    }
}
