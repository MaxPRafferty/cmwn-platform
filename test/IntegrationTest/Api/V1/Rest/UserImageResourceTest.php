<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Test UserImageResourceTest
 *
 * @group User
 * @group Image
 * @group API
 * @group UserImage
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserImageResourceTest extends TestCase
{
    /**
     * @test
     * @ticket CORE-839
     */
    public function testItShouldAllowNeighborsToSeeProfileImages()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('other_teacher');

        $this->dispatch('/user/other_principal/image');

        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertMatchedRouteName('api.rest.user-image');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('image_id', $body, 'Missing image_id from response body for user image');
        $this->assertEquals('profiles/dwtm7optf0qq62vcveef', $body['image_id'], 'Incorrect image_id returned for user');
    }
}
