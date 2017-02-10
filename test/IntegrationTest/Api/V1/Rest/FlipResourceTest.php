<?php

namespace IntegrationTest\Api\V1\Rest;

use Flip\Service\FlipService;
use Flip\Service\FlipServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
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
     * @var FlipServiceInterface
     */
    protected $flipService;

    /**
     * @before
     */
    protected function setUpFlipService()
    {
        $this->flipService = TestHelper::getDbServiceManager()->get(FlipService::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/flip.dataset.php');
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
     * @test
     */
    public function testItShouldCreateAFlip()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/flip', 'POST', ['title' => 'Foo Bar', 'description' => 'baz bat']);
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('flip_id', $body);
        $this->assertEquals('foo-bar', $body['flip_id']);
        $this->assertEquals('Foo Bar', $body['title']);
        $this->assertEquals('baz bat', $body['description']);
    }

    /**
     * @test
     */
    public function testItShouldUpdateAFlip()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/flip/polar-bear', 'PUT', ['title' => 'Polar Bear', 'description' => 'baz bat']);
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

//        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
//        $this->assertArrayHasKey('title', $body);
//        $this->assertArrayHasKey('description', $body);
//        $this->assertArrayHasKey('flip_id', $body);
//        $this->assertEquals('polar-bear', $body['flip_id']);
//        $this->assertEquals('Polar Bear', $body['title']);
//        $this->assertEquals('baz bat', $body['description']);

        $flip = $this->flipService->fetchFlipById('polar-bear');
        $this->assertEquals('Polar Bear', $flip->getTitle());
        $this->assertEquals('baz bat', $flip->getDescription());
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlip()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/flip/polar-bear', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.flip');
        $this->assertControllerName('api\v1\rest\flip\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('detail', $body);
        $this->assertEquals('flip deleted successfully', $body['detail']);
    }

    /**
     * @test
     * @dataProvider unauthorizedActionsDataProvider
     */
    public function testItShould403ForUnauthorizedAccessOfFlips($user, $url, $method, $data)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        $this->dispatch($url, $method, $data);
        $this->assertResponseStatusCode(403);
    }

    /**
     * @return array
     */
    public function unauthorizedActionsDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/flip',
                'POST',
                []
            ],
            1 => [
                'english_student',
                '/flip/polar-bear',
                'PUT',
                []
            ],
            2 => [
                'english_teacher',
                '/flip',
                'POST',
                []
            ],
            3 => [
                'english_teacher',
                '/flip/polar-bear',
                'PUT',
                []
            ],
            4 => [
                'principal',
                '/flip',
                'POST',
                []
            ],
            5 => [
                'principal',
                '/flip/polar-bear',
                'PUT',
                []
            ],
            6 => [
                'english_student',
                '/flip/polar-bear',
                'DELETE',
                []
            ],
            7 => [
                'english_teacher',
                '/flip/polar-bear',
                'DELETE',
                []
            ],
            8 => [
                'principal',
                '/flip/polar-bear',
                'DELETE',
                []
            ],
        ];
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
