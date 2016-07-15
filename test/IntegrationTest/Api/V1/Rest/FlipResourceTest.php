<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * @group DB
 * @group Flip
 * @group Resource
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FlipResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/flip');
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordExceptionFlipId()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/flip/polar-bear');
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }


    /**
     * @test
     * @dataProvider validUserDataProvider
     */
    public function testItShouldReturnFlips($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip');
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);

        $embedded = $body['_embedded'];
        $this->assertArrayHasKey('flip', $embedded);

        $actualIds = [];
        foreach ($embedded['flip'] as $flip) {
            $actualIds[] = $flip['flip_id'];
        }
        $expectedIds = ['polar-bear', 'sea-turtle'];
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     * @dataProvider invalidUserDataProvider
     */
    public function testItShouldReturnErrorStatusUnauthorizedAccessOfAllFlips($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider validUserDataProvider
     */
    public function testItShouldReturnErrorStatusInvalidFlipAccess($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip/foo');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     * @dataProvider validUserDataProvider
     */
    public function testItShouldReturnValidFlipData($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip/polar-bear');
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHaskey('flip_id', $body);
        $this->assertArrayHaskey('title', $body);
        $this->assertArrayHaskey('description', $body);
        $this->assertEquals($body['flip_id'], 'polar-bear');
        $this->assertEquals($body['title'], 'Polar Bear');
        $this->assertEquals(
            $body['description'],
            'The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!'
        );
    }

    /**
     * @test
     * @dataProvider invalidUserDataProvider
     */
    public function testItShouldReturnErrorUnauthorizedAccessOfFlip($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip/polar-bear');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @return array
     */
    public function validUserDataProvider()
    {
        return [
            'English Student' => [
                'english_student'
            ],
            'Math Student' => [
                'math_student'
            ],
            'Super User' => [
                'super_user'
            ],
        ];

    }

    /**
     * @return array
     */
    public function invalidUserDataProvider()
    {
        return [
            'English Teacher' => [
                'english_teacher'
            ],
            'Math Teacher' => [
                'math_teacher'
            ],
        ];
    }
}
