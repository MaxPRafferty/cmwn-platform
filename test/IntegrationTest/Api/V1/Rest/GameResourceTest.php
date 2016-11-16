<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Game\Service\GameServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Class GameResourceTest
 * @package IntegrationTest\Api\V1\Rest
 * @SuppressWarnings(PHPMD)
 */
class GameResourceTest extends TestCase
{
    /**
     * @var GameServiceInterface
     */
    protected $service;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php`');
    }

    /**
     * @before
     */
    public function setUpService()
    {
        $this->markTestIncomplete('wait for it');
        $this->service = TestHelper::getServiceManager()->get(GameServiceInterface::class);
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
     * @param $login
     * @dataProvider loginDataProvider
     */
    public function testItShouldFetchAllGames($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/game');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('game', $body['_embedded']);
        $games = $body['_embedded']['game'];

        $expected = ['animal-id', 'be-bright', 'Monarch'];
        $actual = [];
        foreach ($games as $game) {
            $actual[] = $game['game_id'];
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider loginDataProvider
     */
    public function testItShouldFetchGame($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/game/animal-id');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('game_id', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertArrayHasKey('coming_soon', $body);

        $this->assertEquals('animal-id', $body['game_id']);
        $this->assertEquals('Animal ID', $body['title']);
        $this->assertEquals(
            $body['description'],
            'Can you ID the different kinds of animals? Do you know what plants and animals
                    belong together? Prove it and learn it right here!
                '
        );
        $this->assertEquals(['desktop' => false, 'unity' => false], $body['meta']);
    }

    /**
     * @test
     */
    public function testItShouldCreateGame()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'title' => 'Life Game',
            'description' => 'Game about life',
            'coming_soon' => true,
            'meta' => ['desktop' => true, 'unity' => false]
        ];
        $this->dispatch('/game', 'POST', $postData);
        $this->assertResponseStatusCode(201);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('game_id', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('meta', $body);
        $this->assertArrayHasKey('coming_soon', $body);

        $this->assertEquals('life-game', $body['game_id']);
        $this->assertEquals('Life Game', $body['title']);
        $this->assertEquals(
            $body['description'],
            'Game about life'
        );
        $this->assertEquals(['desktop' => true, 'unity' => false], $body['meta']);
        $this->assertTrue($body['coming_soon']);
    }

    /**
     * @test
     */
    public function testItShouldUpdateGame()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $postData = [
            'title' => 'animal-id',
            'description' => 'animal ids',
            'coming_soon' => false,
            'meta' => ['desktop' => true, 'unity' => false]
        ];
        $this->dispatch('/game/animal-id', 'PUT', $postData);
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');

        $game = $this->service->fetchGame('animal-id');

        $this->assertEquals('animal-id', $game->getGameId());
        $this->assertEquals('animal-id', $game->getTitle());
        $this->assertEquals('animal ids', $game->getDescription());
        $this->assertEquals(['desktop' => true, 'unity' => false], $game->getMeta());
        $this->assertFalse($game->isComingSoon());
    }
    
    /**
     * @test
     */
    public function testItShouldDeleteGame()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/game/animal-id', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');

        try {
            $this->service->fetchGame('animal-id');
            $this->fail('not deleted');
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * @test
     */
    public function testItShould404IfGameNotFound()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/game/foo-bar');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');
    }

    /**
     * @test
     * @param $login
     * @dataProvider postDataProvider
     */
    public function testItShouldNotLetOthersAccessGames($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $postData = [
            'title' => 'animal-id',
            'description' => 'animal ids',
            'coming_soon' => true,
            'meta' => ['desktop' => true, 'unity' => false]
        ];
        $this->dispatch('/game/animal-id', 'PUT', $postData);
        $this->assertResponseStatusCode(403);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');
    }

    /**
     * @test
     * @param $login
     * @dataProvider postDataProvider
     */
    public function testItShouldNotLetOthersCreateGames($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $postData = [
            'title' => 'Life Game',
            'description' => 'Game about life',
            'coming_soon' => true,
            'meta' => ['desktop' => true, 'unity' => false]
        ];
        $this->dispatch('/game', 'POST', $postData);
        $this->assertResponseStatusCode(403);
        $this->assertControllerName('api\v1\rest\game\controller');
        $this->assertMatchedRouteName('api.rest.game');
    }

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            ['super_user'],
            ['english_student'],
            ['other_teacher'],
            ['principal']
        ];
    }

    /**
     * @return array
     */
    public function postDataProvider()
    {
        return [
            ['english_student'],
            ['other_teacher'],
            ['principal']
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
                '/game'
            ],
            1 => [
                'english_student',
                '/game/animal-id'
            ],
        ];
    }
}
