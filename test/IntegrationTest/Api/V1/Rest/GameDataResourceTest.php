<?php

namespace IntegrationTest\Api\V1\Rest;

use Game\SaveGame;
use Game\Service\SaveGameServiceInterface;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Zend\Json\Json;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Class GameDataResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class GameDataResourceTest extends TestCase
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
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/games.dataset.php');
    }

    /**
     * @before
     */
    public function setUpSaveGameService()
    {
        $this->saveService = TestHelper::getDbServiceManager()->get(SaveGameServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldAccessGameDataEndPoint()
    {
        $this->logInUser('super_user');
        $this->injectValidCsrfToken();
        $this->dispatch('/game-data/animal-id');
        $this->assertMatchedRouteName('api.rest.game-data');
        $this->assertControllerName('api\v1\rest\gamedata\controller');
        $this->assertResponseStatusCode(200);
    }

    /**
     * @test
     */
    public function testItShouldReturnAllSaveGameDataForAGame()
    {
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('animal-id');
        $saveGame->setData(['foo' => 'bar']);
        $saveGame->setCreated(new \DateTime());
        $saveGame->setVersion('1.1.1');
        $this->saveService->saveGame($saveGame);

        $saveGameNew = new SaveGame();
        $saveGameNew->setUserId('math_student');
        $saveGameNew->setGameId('animal-id');
        $saveGameNew->setData(['baz' => 'bar']);
        $saveGameNew->setCreated(new \DateTime());
        $saveGameNew->setVersion('1.1.1');
        $this->saveService->saveGame($saveGameNew);

        $this->logInUser('super_user');
        $this->injectValidCsrfToken();
        $this->dispatch('/game-data/animal-id');
        $this->assertMatchedRouteName('api.rest.game-data');
        $this->assertControllerName('api\v1\rest\gamedata\controller');
        $this->assertResponseStatusCode(200);

        $expected = [['math_student', 'animal-id'], ['english_student', 'animal-id']];

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('page_count', $body);
        $this->assertArrayHasKey('_embedded', $body);

        $embedded = $body['_embedded'];
        $this->assertArrayHasKey('items', $embedded);

        $gameData = $embedded['items'];
        $actual = [];
        foreach ($gameData as $game) {
            $this->assertArrayHasKey('user_id', $game);
            $this->assertArrayHasKey('game_id', $game);
            $actual[] = [$game['user_id'], $game['game_id']];
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShouldNotAllowOtherUsersToAccessGameData()
    {
        $this->logInUser('english_student');
        $this->injectValidCsrfToken();
        $this->dispatch('/game-data/animal-id');
        $this->assertMatchedRouteName('api.rest.game-data');
        $this->assertControllerName('api\v1\rest\gamedata\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShould404WhenInvalidGameIdIsUsed()
    {
        $this->logInUser('super_user');
        $this->injectValidCsrfToken();
        $this->dispatch('/game-data/animal');
        $this->assertMatchedRouteName('api.rest.game-data');
        $this->assertControllerName('api\v1\rest\gamedata\controller');
        $this->assertResponseStatusCode(404);
    }

    /**
     * @test
     */
    public function testItShouldReturnAllSaveGameData()
    {
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('animal-id');
        $saveGame->setData(['foo' => 'bar']);
        $saveGame->setCreated(new \DateTime());
        $saveGame->setVersion('1.1.1');
        $this->saveService->saveGame($saveGame);

        $saveGameNew = new SaveGame();
        $saveGameNew->setUserId('math_student');
        $saveGameNew->setGameId('monarch');
        $saveGameNew->setData(['baz' => 'bar']);
        $saveGameNew->setCreated(new \DateTime());
        $saveGameNew->setVersion('1.1.1');
        $this->saveService->saveGame($saveGameNew);

        $this->logInUser('super_user');
        $this->injectValidCsrfToken();
        $this->dispatch('/game-data');
        $this->assertMatchedRouteName('api.rest.game-data');
        $this->assertControllerName('api\v1\rest\gamedata\controller');
        $this->assertResponseStatusCode(200);

        $expected = ['monarch', 'animal-id'];

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);

        $this->assertArrayHasKey('page_count', $body);
        $this->assertArrayHasKey('_embedded', $body);

        $embedded = $body['_embedded'];
        $this->assertArrayHasKey('game-data', $embedded);

        $gameData = $embedded['game-data'];
        $actual = [];
        foreach ($gameData as $game) {
            $this->assertArrayHasKey('game_id', $game);
            $actual[] = $game['game_id'];
        }

        $this->assertEquals($expected, $actual);
    }
}
