<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Game\SaveGame;
use Game\Service\SaveGameServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test SaveGameResourceTest
 *
 * @group IntegrationTest
 * @group API
 * @group Game
 * @group User
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SaveGameResourceTest extends TestCase
{
    /**
     * @var SaveGameServiceInterface
     */
    protected $saveService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/save.dataset.php');
    }

    /**
     * @before
     */
    public function setUpSaveService()
    {
        $this->saveService = TestHelper::getDbServiceManager()->get(SaveGameServiceInterface::class);
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
     * @dataProvider usersAllowedToSaveGamesProvider
     */
    public function testItShouldSaveGameStatusForMe($userName)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($userName);
        $this->dispatch(
            '/user/' .$userName . '/game/monarch',
            'POST',
            ['data' => ['foo' => 'bar'], 'version' => '1.1.1']
        );
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
        $body = $this->getResponse()->getContent();
        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }
        $this->assertArrayHasKey('game_id', $decoded, 'Return does not include the game_id');
        $this->assertArrayHasKey('user_id', $decoded, 'Return does not include the user_id');
        $this->assertArrayHasKey('data', $decoded, 'Return does not include the data');
        $this->assertArrayHasKey('created', $decoded, 'Return does not include the created date');
        $this->assertArrayHasKey('version', $decoded, 'Return does not include the version');
        $this->assertEquals(['foo' => 'bar'], $decoded['data'], 'Data is incorrect for game');
        $this->assertEquals('1.1.1', $decoded['version'], 'Version number is incorrect');
    }
    /**
     * @test
     * @ticket CORE-2351
     * @dataProvider usersNotAllowedToSaveGamesProvider
     */
    public function testItShouldNotSaveGameStatusForMeAdult($userName)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($userName);
        $this->dispatch(
            '/user/' .$userName . '/game/monarch',
            'POST',
            ['data' => ['foo' => 'bar'], 'version' => '1.1.1']
        );
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
    }
    /**
     * @test
     * @dataProvider usersAllowedToSaveGamesProvider
     * @fixme Test for super user when route listener checks permission for super users
     */
    public function testItShouldNotSaveGameForOtherUsers($userName)
    {

        $this->injectValidCsrfToken();
        $this->logInUser($userName);
        $this->dispatch(
            '/user/other_student/game/monarch',
            'POST',
            ['data' => ['foo' => 'bar'], 'version' => '1.1.1']
        );
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
    }
    /**
     * @test
     */
    public function testItShouldReturnBackSaveGameData()
    {
        $date = new \DateTime();
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('monarch');
        $saveGame->setVersion('4.3.2.1');
        $saveGame->setCreated($date);
        $saveGame->setData(['baz' => 'bat']);
        $this->saveService->saveGame($saveGame);
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/game/monarch');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
        $body = $this->getResponse()->getContent();
        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }
        $this->assertArrayHasKey('game_id', $decoded, 'Return does not include the game_id');
        $this->assertArrayHasKey('user_id', $decoded, 'Return does not include the user_id');
        $this->assertArrayHasKey('data', $decoded, 'Return does not include the data');
        $this->assertArrayHasKey('created', $decoded, 'Return does not include the created date');
        $this->assertArrayHasKey('version', $decoded, 'Return does not include the version');
        $this->assertEquals(['baz' => 'bat'], $decoded['data'], 'Data is incorrect for game');
        $this->assertEquals('4.3.2.1', $decoded['version'], 'Version number is incorrect');
    }
    /**
     * @test
     */
    public function testItShouldReturn404WhenThereIsNoSaveData()
    {
        try {
            $this->saveService->fetchSaveGameForUser('english_student', 'monarch');
            $this->fail('This requires no saved data for the english_student');
        } catch (NotFoundException $notFound) {
        }
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/user/english_student/game/monarch');
        $this->assertResponseStatusCode(404);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
    }
    /**
     * @test
     */
    public function testItShouldReplaceDataForGame()
    {
        $date = new \DateTime();
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('monarch');
        $saveGame->setVersion('4.3.2.1');
        $saveGame->setCreated($date);
        $saveGame->setData(['baz' => 'bat']);
        $this->saveService->saveGame($saveGame);
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch(
            '/user/english_student/game/monarch',
            'POST',
            ['data' => ['foo' => 'bar'], 'version' => '1.1.1']
        );
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
        $body = $this->getResponse()->getContent();
        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }
        $this->assertArrayHasKey('game_id', $decoded, 'Return does not include the game_id');
        $this->assertArrayHasKey('user_id', $decoded, 'Return does not include the user_id');
        $this->assertArrayHasKey('data', $decoded, 'Return does not include the data');
        $this->assertArrayHasKey('created', $decoded, 'Return does not include the created date');
        $this->assertArrayHasKey('version', $decoded, 'Return does not include the version');
        $this->assertEquals(['foo' => 'bar'], $decoded['data'], 'Data is incorrect for game');
        $this->assertEquals('1.1.1', $decoded['version'], 'Version number is incorrect');
    }
    /**
     * @test
     */
    public function testItShouldDeleteSavedGameData()
    {
        $date = new \DateTime();
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('monarch');
        $saveGame->setVersion('4.3.2.1');
        $saveGame->setCreated($date);
        $saveGame->setData(['baz' => 'bat']);
        $this->saveService->saveGame($saveGame);
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch(
            '/user/english_student/game/monarch',
            'DELETE'
        );
        $this->assertResponseStatusCode(204);
        $this->assertMatchedRouteName('api.rest.save-game');
        $this->assertControllerName('api\v1\rest\savegame\controller');
        try {
            $this->saveService->fetchSaveGameForUser('english_student', 'monarch');
            $this->fail('The service did not delete the game for the user');
        } catch (NotFoundException $notFound) {
        }
    }
    /**
     * @return array
     */
    public function usersAllowedToSaveGamesProvider()
    {
        return [
            'English student' => [
                'user_name' => 'english_student',
            ],
            'Math student'    => [
                'user_name' => 'math_student',
            ],
        ];
    }
    /**
     * @return array
     */
    public function usersNotAllowedToSaveGamesProvider()
    {
        return [
            'English Teacher' => [
                'user_name' => 'english_teacher',
            ],
            'Math teacher'    => [
                'user_name' => 'math_teacher',
            ],
            'Principal'       => [
                'user_name' => 'principal',
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
            0 => [
                'english_student',
                '/user/english_student/game/monarch',
                'DELETE'
            ],
        ];
    }
}
