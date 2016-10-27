<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Flag\Flag;
use Flag\Service\FlagService;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Child;
use User\User;
use Zend\Json\Json;

/**
 * Class FlagResourceTest
 * @package IntegrationTest\Api\V1\Rest
 * @group Db
 * @group Flag
 * @group FlagResource
 * @group API
 * @group IntegrationTest
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlagResourceTest extends TestCase
{
    /**
     * @var FlagService
     */
    protected $flagService;

    /**
     * @before
     */
    public function setUpFlagService()
    {
        $this->flagService = TestHelper::getServiceManager()->get(FlagService::class);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('super_user');
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
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     * @dataProvider changePasswordDataProvider
     */
    public function testItShouldCheckChangePasswordException($user, $url, $method = 'GET', $params = [])
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($user);
        $this->assertChangePasswordException($url, $method, $params);
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

        $postData = [
            'flagger' => new Child(['user_id'=>'math_student']),
            'flaggee' => new Child(['user_id'=>'english_student']),
            'reason'  => 'bar',
            'url'     => '/foo'
        ];

        $flag = new Flag($postData);
        $this->flagService->saveFlag($flag);

        $this->dispatch('/flag/' . $flag->getFlagId());
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('flag_id', $body, 'Invalid flag entity returned');
        $this->assertEquals($flag->getFlagId(), $body['flag_id']);
        $this->assertArrayHasKey('reason', $body, 'Invalid flag entity returned');
        $this->assertEquals($flag->getReason(), $body['reason']);
        $this->assertArrayHasKey('url', $body, 'Invalid flag entity returned');
        $this->assertEquals($flag->getUrl(), $body['url']);
    }

    /**
     * @test
     * @dataProvider loginDataProvider
     */
    public function testItShouldNotAllowOthersToViewFlaggedImageById($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $postData = [
            'flagger' => new Child(['user_id'=>$login]),
            'flaggee' => new Child(['user_id'=>'other_student']),
            'reason'  => 'bar',
            'url'     => '/foo'
        ];

        $flag = new Flag($postData);
        $this->flagService->saveFlag($flag);

        $this->dispatch('/flag/' . $flag->getFlagId());
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider loginDataProvider
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
            ['math_student', 'english_student', '/asdf', 'inappropriate'],
        ];
        $actual = [];
        $index = 0;
        foreach ($flags as $flag) {
            $actual[$index][] = $flag['flagger']['user_id'];
            $actual[$index][] = $flag['flaggee']['user_id'];
            $actual[$index][] = $flag['url'];
            $actual[$index++][] = $flag['reason'];
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
            'flaggee' => ['user_id'=>'english_student', 'type' => User::TYPE_CHILD],
            'reason'  => 'bar',
            'url'     => '/foo'
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
    public function testItShouldUpdateFlaggedImage()
    {
        $this->markTestIncomplete("No users can edit flag for now");
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');
        $postData = [
            'flagger' => new Child(['user_id'=>'math_student']),
            'flaggee' => new Child(['user_id'=>'english_student']),
            'reason'  => 'bar',
            'url'     => '/foo'
        ];
        $flag = new Flag($postData);
        $this->flagService->saveFlag($flag);
        $postData = array_merge($postData, $flag->getArrayCopy());
        $postData['reason'] = 'troll';
        $this->dispatch(
            '/flag/'.$flag->getFlagId(),
            'PUT',
            $postData
        );
        $this->assertControllerName('api\v1\rest\flag\controller');
        $this->assertMatchedRouteName('api.rest.flag');
        $this->assertResponseStatusCode(200);
        $flag = $this->flagService->fetchFlag($flag->getFlagId());
        $this->assertEquals($flag->getReason(), 'troll');
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlaggedImage()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'flagger' => new Child(['user_id'=>'math_student']),
            'flaggee' => new Child(['user_id'=>'english_student']),
            'reason'  => 'bar',
            'url'     => '/foo'
        ];
        $flag = new Flag($postData);
        $this->flagService->saveFlag($flag);

        $this->dispatch('/flag/' . $flag->getFlagId(), 'DELETE');
        $this->assertResponseStatusCode(200);

        try {
            $this->flagService->fetchFlag($flag->getFlagId());
            $this->fail("flag not deleted");
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * @test
     * @dataProvider loginDataProvider
     */
    public function testItShouldNotAllowOthersToDeleteFlaggedImages($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $postData = [
            'flagger' => new Child(['user_id'=>$login]),
            'flaggee' => new Child(['user_id'=>'english_student']),
            'reason'  => 'bar',
            'url'     => '/foo'
        ];
        $flag = new Flag($postData);
        $this->flagService->saveFlag($flag);

        $this->dispatch('/flag/' . $flag->getFlagId(), 'DELETE');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            ['english_student'],
            ['english_teacher'],
            ['principal'],
        ];
    }

    /**
     * @return array
     */
    public function invalidPostDataProvider()
    {
        return [
            [
                'flaggee' => new Child(['user_id'=>'english_student']),
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
                'flaggee' => new Child(['user_id'=>'english_student']),
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
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/flip'
            ],
            1 => [
                'english_student',
                '/flip/polar-bear'
            ],
        ];
    }
}
