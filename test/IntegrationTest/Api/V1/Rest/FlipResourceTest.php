<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

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
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/flip.dataset.php');
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
     * @dataProvider validUserDataProvider
     */
    public function testItShouldReturnFlips($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/flip');
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

        $this->assertResponseStatusCode(200);
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
            'The magnificent Polar Bear is in danger of becoming extinct.' .
            '  Get the scoop and go offline for the science on how they stay warm!',
            $body['description']
        );
    }

    /**
     * @test
     */
    public function testItShouldReturnErrorUnauthorizedAccessOfFlip()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/flip/polar-bear');
        $this->assertResponseStatusCode(401);
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
