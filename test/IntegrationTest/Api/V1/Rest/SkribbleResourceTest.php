<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Skribble\Service\SkribbleServiceInterface;
use Skribble\Skribble;
use Zend\Json\Json;

/**
 * Test SkribbleResourceTest
 *
 * @group API
 * @group Skribble
 * @group Db
 * @group IntegrationTest
 * @group SkribbleService
 * @group SkribbleRule
 * @group Skribble
 * @group Friend
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleResourceTest extends TestCase
{
    /**
     * @var SkribbleServiceInterface
     */
    protected $skribbleService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../../DataSets/skribble.dataset.php';

        return new ArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpSkribbleService()
    {
        $this->skribbleService = TestHelper::getServiceManager()->get(SkribbleServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllForSkribble()
    {
        $expectedIds = ['foo-bar', 'baz-bat', 'qux-thud'];
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('skribble', $embedded, 'Embedded does not contain any skribbles');

        $actualIds = [];
        foreach ($embedded['skribble'] as $skribble) {
            $actualSkribble = new Skribble($skribble);
            $actualIds[]    = $actualSkribble->getSkribbleId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Skribbles');
    }

    /**
     * @test
     */
    public function testItShouldFetchAllSentForSkribble()
    {
        $expectedIds = ['baz-bat', 'qux-thud'];
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble?status=sent');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('skribble', $embedded, 'Embedded does not contain any skribbles');

        $actualIds = [];
        foreach ($embedded['skribble'] as $skribble) {
            $actualSkribble = new Skribble($skribble);
            $actualIds[]    = $actualSkribble->getSkribbleId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Sent Skribbles');
    }

    /**
     * @test
     */
    public function testItShouldFetchAllReceivedForSkribble()
    {
        $expectedIds = ['baz-bat', 'fizz-buzz'];
        $this->logInUser('math_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/math_student/skribble?status=received');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('skribble', $embedded, 'Embedded does not contain any skribbles');

        $actualIds = [];
        foreach ($embedded['skribble'] as $skribble) {
            $actualSkribble = new Skribble($skribble);
            $actualIds[]    = $actualSkribble->getSkribbleId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Received Skribbles');
    }

    /**
     * @test
     */
    public function testItShouldFetchAllDraftForSkribble()
    {
        $expectedIds = ['foo-bar'];
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble?status=draft');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('skribble', $embedded, 'Embedded does not contain any skribbles');

        $actualIds = [];
        foreach ($embedded['skribble'] as $skribble) {
            $actualSkribble = new Skribble($skribble);
            $actualIds[]    = $actualSkribble->getSkribbleId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Draft Skribbles');
    }

    /**
     * @test
     */
    public function testItShouldNotAllowFetchingOtherSkribbles()
    {
        $this->markTestSkipped('skribbles is open until oauth is implemented');
        $this->logInUser('manchuck');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble?status=draft');

        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');
    }

    /**
     * @test
     */
    public function testItShouldFetchCompletedSkribble()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/baz-bat');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');


        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Skribble ID missing from skribble'
        );

        $this->assertEquals('baz-bat', $decoded['skribble_id'], 'GET on skribble did not return back correct skribble');

        $this->assertArrayHasKey(
            'friend_to',
            $decoded,
            'friend_to missing from skirbble'
        );

        $this->assertEquals(
            'math_student',
            $decoded['friend_to'],
            'GET on skribble did not return back correct friend'
        );

        $this->assertArrayHasKey(
            'status',
            $decoded,
            'status missing from skribble'
        );

        $this->assertEquals(
            'COMPLETE',
            $decoded['status'],
            'GET on skribble did not return back correct status'
        );
    }

    /**
     * @test
     */
    public function testItShouldFetchNonCompletedSkribble()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');


        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Skribble ID missing from skribble'
        );

        $this->assertEquals('foo-bar', $decoded['skribble_id'], 'GET on skribble did not return back correct skribble');

        $this->assertArrayHasKey(
            'friend_to',
            $decoded,
            'friend_to missing from skirbble'
        );

        $this->assertNull(
            $decoded['friend_to'],
            'GET on skribble did not return back correct friend'
        );

        $this->assertArrayHasKey(
            'status',
            $decoded,
            'status missing from skribble'
        );

        $this->assertEquals(
            'NOT_COMPLETE',
            $decoded['status'],
            'GET on skribble did not return back correct status'
        );
    }

    /**
     * @test
     */
    public function testItShouldAllowYouToFetchSkribbleYouReceived()
    {
        $this->logInUser('math_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/math_student/skribble/baz-bat');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');


        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Invalid Response from API;'
        );

        $this->assertEquals('baz-bat', $decoded['skribble_id']);
    }

    /**
     * @test
     */
    public function testItShouldAllowYouToFetchSkribbleYouSent()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/baz-bat');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Invalid Response from API;'
        );

        $this->assertEquals('baz-bat', $decoded['skribble_id']);
    }

    /**
     * @test
     */
    public function testItShouldAllowYouToFetchSkribbleYouDoNotOwn()
    {
        // TODO Fix this once the service is locked down
        $this->markTestSkipped('Service is open for the moment');
        $this->logInUser('manchuck');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/manchuck/skribble/foo-bar');

        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');
    }

    /**
     * @test
     */
    public function testItShouldAllowUsersToCreateSkribble()
    {
        $skribbleData = [
            'version'     => '1',
            'url'         => null,
            'status'      => 'NOT_COMPLETE',
            'friend_to'   => null,
            'read'        => 0,
            'rules'       => [
                'background' => null,
                'effect'     => null,
                'sound'      => null,
                'items'      => [],
                'messages'   => [],
            ],
        ];

        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble', 'POST', $skribbleData);

        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Invalid Response from API;'
        );

        $this->skribbleService->fetchSkribble($decoded['skribble_id']);
    }

    /**
     * @test
     */
    public function testItShouldAllowSkribblesToBeUpdated()
    {
        $skribbleData = [
            'version'     => '1',
            'url'         => null,
            'status'      => 'NOT_COMPLETE',
            'friend_to'   => 'math_student',
            'rules'       => [
                'background' => null,
                'effect'     => null,
                'sound'      => null,
                'items'      => [],
                'messages'   => [],
            ],
        ];

        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar', 'PUT', $skribbleData);

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Invalid Response from API;'
        );

        $this->assertEquals('foo-bar', $decoded['skribble_id'], 'Skribble ID was updated');
        $changed = $this->skribbleService->fetchSkribble($decoded['skribble_id']);
        $this->assertEquals(
            'math_student',
            $changed->getFriendTo(),
            'API did not update the skribble'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotAllowSkribblesToBeUpdatedFromOtherUsers()
    {
        // TODO Fix this once the service is locked down
        $this->markTestSkipped('Service is open for the moment');

        $skribbleData = [
            'version'     => '1',
            'url'         => null,
            'status'      => 'NOT_COMPLETE',
            'friend_to'   => 'math_student',
            'rules'       => [
                'background' => null,
                'effect'     => null,
                'sound'      => null,
                'items'      => [],
                'messages'   => [],
            ],
        ];

        $this->logInUser('manchuck');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar', 'PUT', $skribbleData);

        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $changed = $this->skribbleService->fetchSkribble('foo-bar');
        $this->assertEquals(
            'math_student',
            $changed->getFriendTo(),
            'API did not update the skribble'
        );
    }

    /**
     * @test
     */
    public function testItShouldDeleteSkribble()
    {

        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar', 'DELETE');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $this->setExpectedException(NotFoundException::class);
        $this->skribbleService->fetchSkribble('foo-bar');
    }

    /**
     * @test
     */
    public function testItShouldNotAllowYouToDeleteSkribbleYouDontOwn()
    {
        // TODO Fix this once the service is locked down
        $this->markTestSkipped('Service is open for the moment');

        $this->logInUser('manchuck');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar', 'DELETE');

        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $this->setExpectedException(NotFoundException::class);
        $this->skribbleService->fetchSkribble('foo-bar');
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribbleAsRead()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/english_student/skribble/foo-bar', 'PATCH', ['read' => 0]);

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            'skribble_id',
            $decoded,
            'Invalid Response from API;'
        );

        $this->assertEquals('foo-bar', $decoded['skribble_id'], 'Skribble ID was not marked as read');
        $changed = $this->skribbleService->fetchSkribble($decoded['skribble_id']);
        $this->assertTrue(
            $changed->isRead(),
            'API did not PATCH the skribble to be marked as read'
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnBackReadOnlySkribbles()
    {
        $expectedIds = ['fizz-buzz'];
        $this->logInUser('math_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/user/math_student/skribble?read=true');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.skribble');
        $this->assertControllerName('api\v1\rest\skribble\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Skribble Response');

            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('skribble', $embedded, 'Embedded does not contain any skribbles');

        $actualIds = [];
        foreach ($embedded['skribble'] as $skribble) {
            $actualSkribble = new Skribble($skribble);
            $actualIds[]    = $actualSkribble->getSkribbleId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Sent Skribbles');
    }
}
