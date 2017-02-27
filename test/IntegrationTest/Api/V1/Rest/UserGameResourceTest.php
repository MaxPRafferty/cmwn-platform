<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Game\Game;
use Game\Service\UserGameServiceInterface;
use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\TestHelper;
use User\Adult;
use Zend\Json\Json;

/**
 * Integration tests for UserGameResource
 */
class UserGameResourceTest extends AbstractApigilityTestCase
{
    /**
     * @var UserGameServiceInterface
     */
    protected $userGameService;

    /**
     * @before
     */
    public function setUpService()
    {
        $this->userGameService = TestHelper::getDbServiceManager()->get(UserGameServiceInterface::class);
    }

    /**
     * @inheritdoc
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php');
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
    public function testItShouldAttachGameToUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user/english_student/game/be-bright', 'POST');
        $this->assertResponseStatusCode(201);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
        try {
            $this->userGameService->fetchGameForUser(
                new Adult(['user_id' => 'english_student']),
                new Game(['game_id' => 'be-bright'])
            );
        } catch (NotFoundException $nf) {
            $this->fail('game not attached to user');
        }
    }
    /**
     * @test
     */
    public function testItShouldDetachGameFromUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/user/english_student/game/Monarch', 'DELETE');
        $this->assertResponseStatusCode(204);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
        try {
            $this->userGameService->fetchGameForUser(
                new Adult(['user_id' => 'english_student']),
                new Game(['game_id' => 'Monarch'])
            );
            $this->fail('game not detached from user');
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * @test
     * @param $user
     * @param $expected
     * @dataProvider userGameDataProvider
     */
    public function testItShouldFetchAllGamesForUserExcludingDeletedGames($user, $expected)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        $this->dispatch('/user/' . $user . '/game');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('game', $body['_embedded']);
        $games = $body['_embedded']['game'];
        $actual = [];
        foreach ($games as $game) {
            $this->assertArrayHasKey('game_id', $game);
            $actual[] = $game['game_id'];
        }
        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     * @param $user
     * @param $url
     * @param string $method
     * @dataProvider unauthorizedRouteDataProvider
     */
    public function testItShouldNotLetOthersAttachOrDetachGames($user, $url, $method = 'GET')
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);
        $this->dispatch($url, $method);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldFetchGameForUser()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/game/Monarch');
        $this->assertResponseStatusCode(200);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('game_id', $body);
        $this->assertEquals('Monarch', $body['game_id']);
        $this->assertEquals(
            'Monarch Butterflies are crucial for the environment' .
            ' yet they are endangered! This is your spot!',
            $body['description']
        );
        $this->assertEquals('Monarch', $body['title']);
    }

    /**
     * @test
     */
    public function testItShould404WhenADeletedGameIsFetched()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/game/deleted-game');
        $this->assertResponseStatusCode(404);
        $this->assertControllerName('api\v1\rest\usergame\controller');
        $this->assertMatchedRouteName('api.rest.user-game');
    }

    /**
     * @return array
     */
    public function userGameDataProvider()
    {
        return [
            [
                'english_student',
                ['animal-id', 'Monarch'],
            ],
            [
                'math_student',
                ['animal-id']
            ],
        ];
    }

    /**
     * @return array
     */
    public function unauthorizedRouteDataProvider()
    {
        return [
            [
                'english_student',
                '/user/english_student/game/be-bright',
                'POST'
            ],
            [
                'english_student',
                '/user/english_student/game/be-bright',
                'DELETE'
            ],
            [
                'english_teacher',
                '/user/english_teacher/game/be-bright',
                'POST'
            ],
            [
                'english_teacher',
                '/user/english_teacher/game/be-bright',
                'DELETE'
            ],
            [
                'principal',
                '/user/principal/game/be-bright',
                'POST'
            ],
            [
                'principal',
                '/user/principal/game/be-bright',
                'DELETE'
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
                '/user/english_student/game/monarch'
            ],
            1 => [
                'english_student',
                '/user/english_student/game/monarch',
                'POST',
                ['data' => ['foo' => 'bar'], 'version' => '1.1.1']
            ],
            2 => [
                'english_student',
                '/user/english_student/game/monarch',
                'DELETE'
            ],
        ];
    }
}
