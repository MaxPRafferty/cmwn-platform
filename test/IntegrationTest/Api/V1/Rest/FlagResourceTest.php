<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Flag\Flag;
use Flag\Service\FlagService;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use IntegrationTest\DataSets\ArrayDataSet;
use User\Child;
use User\User;
use Zend\Json\Json;

/**
 * Class FlagResourceTest
 *
 * @package IntegrationTest\Api\V1\Rest
 * @group   Db
 * @group   Flag
 * @group   FlagResource
 * @group   API
 * @group   IntegrationTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlagResourceTest extends TestCase
{
    /**
     * @var FlagService
     */
    protected $flagService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/flag.dataset.php');
    }

    /**
     * @before
     */
    public function setUpFlagService()
    {
        $this->flagService = TestHelper::getDbServiceManager()->get(FlagService::class);
    }

    /**
     * @test
     * @dataProvider basicDataProvider
     */
    public function testItShouldCheckCsrf($user)
    {
        $this->logInUser($user);
        $this->dispatch('/flag');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldCheckIfTheUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/flag');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     *
     * @param string $user
     *
     * @dataProvider basicDataProvider
     */
    public function testItShouldCheckChangePasswordException($user)
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException('/flag');
    }

    /**
     * @test
     */
    public function testItShouldAccessFlagEndPoint()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/flag');
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function testItShouldFetchFlagById()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/flag/flagged-image');
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('flag_id', $body, 'Invalid flag entity returned');
        $this->assertArrayHasKey('reason', $body, 'Invalid flag entity returned');
        $this->assertArrayHasKey('url', $body, 'Invalid flag entity returned');

        $this->assertEquals('flagged-image', $body['flag_id']);
        $this->assertEquals('Offensive ;)', $body['reason']);
        $this->assertEquals('http://read.bi/2clh0wi', $body['url']);
    }

    /**
     * @test
     * @dataProvider noAccessDataProvider
     */
    public function testItShouldNotAllowOthersToViewFlaggedImageById($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flag/flagged-image');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider noAccessDataProvider
     */
    public function testItShouldNotAllowOthersToViewFlaggedImages($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/flag');
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlaggedImages()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/flag');
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('flags', $body['_embedded']);

        $flags = $body['_embedded']['flags'];

        $expected = [
            [
                'flag_id' => 'flagged-image',
                'flagger' => 'math_student',
                'flaggee' => 'english_student',
                'url'     => 'http://read.bi/2clh0wi',
                'reason'  => 'Offensive ;)',
            ],
        ];

        $actual = [];
        foreach ($flags as $flag) {
            $actualFlag = $flag;
            unset($actualFlag['_links']);
            $actualFlag['flagger'] = $flag['flagger']['user_id'];
            $actualFlag['flaggee'] = $flag['flaggee']['user_id'];
            array_push($actual, $actualFlag);
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldFlagAnImageWhenPostDataValid()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $postData = [
            'flaggee' => ['user_id' => 'english_student', 'type' => User::TYPE_CHILD],
            'reason'  => 'bar',
            'url'     => '/foo',
        ];
        $this->dispatch(
            '/flag',
            'POST',
            $postData
        );
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(201);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('flag_id', $body, 'Valid entity not returned');
        $flagId = $body['flag_id'];
        try {
            $result = $this->flagService->fetchFlag($flagId);
            $this->assertEquals($result->getFlagger()->getUserId(), 'english_teacher');
            $this->assertEquals($result->getFlaggee()->getUserId(), 'english_student');
            $this->assertEquals($result->getUrl(), '/foo');
            $this->assertEquals($result->getReason(), 'bar');
        } catch (NotFoundException $nf) {
            $this->fail("Image not flagged");
        }
    }

    /**
     * @test
     * @dataProvider invalidPostDataProvider
     */
    public function testItShouldErrorWhenPostDataInvalid($flaggee, $reason, $url)
    {
        $postData = [
            'flaggee' => $flaggee,
            'reason'  => $reason,
            'url'     => $url,
        ];
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');
        $this->dispatch(
            '/flag',
            'POST',
            $postData
        );
        $this->assertResponseStatusCode(422);
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlaggedImage()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/flag/flagged-image', 'DELETE');
        $this->assertResponseStatusCode(200);

        try {
            $this->flagService->fetchFlag('flagged-image');
            $this->fail("flag not deleted");
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * @test
     * @dataProvider noAccessDataProvider
     */
    public function testItShouldNotAllowOthersToDeleteFlaggedImages($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flag/flagged-image', 'DELETE');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @return array
     */
    public function invalidPostDataProvider()
    {
        return [
            [
                'flaggee' => new Child(['user_id' => 'english_student']),
                'reason'  => null,
                'url'     => null,
            ],
            [
                'flaggee' => null,
                'reason'  => null,
                'url'     => '/foo',
            ],
            [
                'flaggee' => null,
                'reason'  => 'bar',
                'url'     => null,
            ],
            [
                'flaggee' => new Child(['user_id' => 'english_student']),
                'reason'  => 'bar',
                'url'     => null,
            ],
            [
                'flaggee' => 'english_student',
                'reason'  => null,
                'url'     => '/foo',
            ],
            [
                'flaggee' => null,
                'reason'  => 'bar',
                'url'     => '/foo',
            ],
        ];
    }

    /**
     * @return array
     */
    public function basicDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
            ],
            'Principal'       => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
            'Super User'      => [
                'super_user',
            ],
        ];
    }

    /**
     * @return array
     */
    public function noAccessDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
            ],
            'Principal'       => [
                'principal',
            ],
            'English Teacher' => [
                'english_teacher',
            ],
        ];
    }
}
