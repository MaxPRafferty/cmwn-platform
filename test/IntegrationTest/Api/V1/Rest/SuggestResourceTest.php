<?php

namespace IntegrationTest\Api\V1\Rest;

use Friend\Service\FriendServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Suggest\Engine\SuggestionEngine;
use Zend\Json\Json;

/**
 * Test SuggestResourceTest
 *
 * @group Suggest
 * @group IntegrationTest
 * @group SuggestedService
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
     * @var SuggestionEngine
     */
    protected $suggestionEngine;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/suggest.dataset.php');
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->friendService = TestHelper::getDbServiceManager()->get(FriendServiceInterface::class);
        $this->suggestionEngine = TestHelper::getDbServiceManager()->get(SuggestionEngine::class);
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     *
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckChangePasswordException($user, $method = 'GET', $params = [])
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException('/user/' . $user. '/suggest', $method, $params);
    }

    /**
     * @test
     * @ticket CORE-701
     * @ticket CORE-703
     * @ticket CORE-2669
     * @ticket CORE-558
     * @dataProvider suggestedFriendsProvider
     */
    public function testItShouldReturnSuggestionsForChild($user, $expected)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        $this->dispatch('/user/' . $user. '/suggest');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('suggest', $body['_embedded']);
        $actualSuggestion = [];
        foreach ($body['_embedded']['suggest'] as $suggestData) {
            $actualSuggestion[] = $suggestData['suggest_id'];
        }


        $this->assertEquals($expected, $actualSuggestion);
    }

    /**
     * @test
     */
    public function testItShouldReturnNotAuthorizedForAdult()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $this->dispatch('/user/english_teacher/suggest');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.suggest');
        $this->assertControllerName('api\v1\rest\suggest\controller');
    }

    /**
     * @return array
     */
    public function suggestedFriendsProvider()
    {
        return [
            'English Student' => [
                'english_student',
                ['english_student_1', 'english_student_2'],
            ],
            'Math Student' => [
                'math_student',
                [],
            ],
            'English Student 1' => [
                'english_student_1',
                ['english_student'],
            ],
            'English Student 2' => [
                'english_student_2',
                ['english_student'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
            ],
            'English Student 1' => [
                'english_student_1',
            ],
            'English Student 2' => [
                'english_student_2',
            ],
            'Math Student' => [
                'math_student',
            ],
            'Principal' => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
        ];
    }
}
