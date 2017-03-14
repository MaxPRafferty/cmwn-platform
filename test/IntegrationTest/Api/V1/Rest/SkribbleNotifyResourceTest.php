<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\LoginBasicAuthTrait;
use IntegrationTest\TestHelper;
use Skribble\Service\SkribbleServiceInterface;
use Skribble\SkribbleInterface;

/**
 * Test SkribbleNotifyResourceTest
 *
 * @group Skribble
 * @group API
 * @group DB
 * @group IntegrationTest
 * @group AWS
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SkribbleNotifyResourceTest extends TestCase
{
    use LoginBasicAuthTrait;

    /**
     * @var SkribbleServiceInterface
     */
    protected $skribbleService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/skribble.dataset.php');
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
    public function testItShouldUpdateSkribbleToCompleteWithSuccess()
    {
        $this->loginBasicAuth($this->getRequest());
        $this->dispatch('/user/english_student/skribble/foo-bar/notice', 'POST', ['status' => 'success']);

        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.skribble-notify');
        $this->assertControllerName('api\v1\rest\skribblenotify\controller');

        $skribble = $this->skribbleService->fetchSkribble('foo-bar');
        $this->assertEquals(
            SkribbleInterface::STATUS_COMPLETE,
            $skribble->getStatus(),
            'Skribble Notification API did not set skribble to complete'
        );
    }

    /**
     * @test
     */
    public function testItShouldUpdateSkribbleToCompleteWithError()
    {
        $this->loginBasicAuth($this->getRequest());
        $this->dispatch('/user/english_student/skribble/foo-bar/notice', 'POST', ['status' => 'error']);

        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.skribble-notify');
        $this->assertControllerName('api\v1\rest\skribblenotify\controller');

        $skribble = $this->skribbleService->fetchSkribble('foo-bar');
        $this->assertEquals(
            SkribbleInterface::STATUS_ERROR,
            $skribble->getStatus(),
            'Skribble Notification API did not set skribble to error®'
        );
    }

    /**
     * @test
     */
    public function testItShould404WhenSkribbleIsNotFound()
    {
        $this->loginBasicAuth($this->getRequest());
        $this->dispatch('/user/english_student/skribble/manchuck/notice', 'POST', ['status' => 'error']);

        $this->assertResponseStatusCode(404);
        $this->assertMatchedRouteName('api.rest.skribble-notify');
        $this->assertControllerName('api\v1\rest\skribblenotify\controller');
    }
}
