<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;

/**
 * Test FlipUserResourceTest
 * @group DB
 * @group FlipUser
 * @group Resource
 */
class FlipUserResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('english_student');
        $this->dispatch('/user/english_student/flip');
        $this->assertResponseStatusCode(401);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('RESET_PASSWORD', $body['detail']);
    }

    /**
     * @test
     * @dataProvider validUserDataProvider
     */
    public function testItShouldCheckIfUserLoggedInIsTheOneRequestingFlip($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/user/english_student/flip');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider validUserDataProvider
     */
    public function testItShouldCheckIfRouteUrlIsCorrect($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/user/manchuck/flip');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function testItShouldReturnValidUserFlips()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/user/math_student/flip');
        $this->assertMatchedRouteName('api.rest.flip-user');
        $this->assertControllerName('api\v1\rest\flipuser\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $embedded = $body['_embedded'];
        $this->assertArrayHasKey('flip_user', $embedded);
        $flips = $embedded['flip_user'];
        $this->assertArrayHasKey('flip_id', $flips[0]);

        $expectedids = ['polar-bear', 'sea-turtle'];
        $actualids = [];
        foreach ($flips as $flip) {
            $actualids[] = $flip['flip_id'];
        }
        $this->assertEquals($actualids, $expectedids);
    }

    /**
     * @test
     */
    public function testItShouldReturnFlipDataForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/user/math_student/flip/polar-bear');
        $this->assertMatchedRouteName('api.rest.flip-user');
        $this->assertControllerName('api\v1\rest\flipuser\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('items', $body['_embedded']);
        $embedded = $body['_embedded']['items'];

        $this->assertArrayHasKey('flip_id', $embedded[0]);
        $this->assertArrayHasKey('title', $embedded[0]);
        $this->assertArrayHasKey('description', $embedded[0]);
        $this->assertEquals($embedded[0]['flip_id'], "polar-bear");
        $this->assertEquals($embedded[0]['title'], "Polar Bear");
        $this->assertEquals(
            $embedded[0]['description'],
            "The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!"
        );
    }

    /**
     * @test
     */
    public function testItShouldCreateValidFlipForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/user/math_student/flip', 'POST', ['flip_id' => 'polar-bear']);
        $this->assertMatchedRouteName('api.rest.flip-user');
        $this->assertControllerName('api\v1\rest\flipuser\controller');
        $this->assertResponseStatusCode(201);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('items', $body['_embedded']);
        $embedded = $body['_embedded']['items'];

        $this->assertArrayHasKey('flip_id', $embedded[0]);
        $this->assertArrayHasKey('title', $embedded[0]);
        $this->assertArrayHasKey('description', $embedded[0]);
        $this->assertEquals($embedded[0]['flip_id'], "polar-bear");
        $this->assertEquals($embedded[0]['title'], "Polar Bear");
        $this->assertEquals(
            $embedded[0]['description'],
            "The magnificent Polar Bear is in danger of becoming extinct. Get the scoop and go offline for the science on how they stay warm!"
        );
    }

    /**
     * @return array
     */
    public function validUserDataProvider()
    {
        return [
            'English Teacher' => [
                'english_teacher'
            ],
            'Math Student' => [
                'math_student'
            ],
            'Principal' => [
                'principal'
            ],
        ];

    }
}
