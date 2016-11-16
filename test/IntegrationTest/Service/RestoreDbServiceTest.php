<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use Asset\Service\UserImageServiceInterface;
use Flip\Service\FlipUserServiceInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Game\SaveGame;
use Game\Service\SaveGameServiceInterface;
use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use RestoreDb\Service\RestoreDbService;
use Suggest\Service\SuggestedServiceInterface;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Class RestoreDbServiceTest
 * @package IntegrationTest\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RestoreDbServiceTest extends AbstractApigilityTestCase
{
    /**
     * @var array
     */
    protected $actualData;

    /**
     * @var RestoreDbService
     */
    protected $restoreService;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var UserImageServiceInterface
     */
    protected $userImageService;

    /**
     * @var FlipUserServiceInterface
     */
    protected $flipUserService;

    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * @var SaveGameServiceInterface
     */
    protected $saveGameService;

    /**
     * @var SuggestedServiceInterface
     */
    protected $suggestedService;

    /**
     * @before
     */
    public function setUpActualData()
    {
        $this->actualData = include __DIR__ . '/../../../config/autoload/test-data.global.php';
        $this->actualData = isset($this->actualData['test-data']) ? $this->actualData['test-data'] : [];
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->restoreService = TestHelper::getDbServiceManager()->get(RestoreDbService::class);
        $this->userService = TestHelper::getDbServiceManager()->get(UserServiceInterface::class);
        $this->userImageService = TestHelper::getDbServiceManager()->get(UserImageServiceInterface::class);
        $this->flipUserService = TestHelper::getDbServiceManager()->get(FlipUserServiceInterface::class);
        $this->friendService = TestHelper::getDbServiceManager()->get(FriendServiceInterface::class);
        $this->saveGameService = TestHelper::getDbServiceManager()->get(SaveGameServiceInterface::class);
        $this->suggestedService = TestHelper::getDbServiceManager()->get(SuggestedServiceInterface::class);
    }

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $dataSet = include __DIR__ . '/../DataSets/restore.dataset.php';
        return new ArrayDataSet($dataSet);
    }

    /**
     * @test
     */
    public function testItShouldRestoreDbStateForUsers()
    {
        $user = $this->actualData['users'][0];

        $this->userService->updateUserName(new Child(['user_id' => $user['user_id']]), 'foo');

        $userBeforeRestore = $this->userService->fetchUser($user['user_id']);

        $this->assertEquals('foo', $userBeforeRestore->getUserName());

        $this->restoreService->runDbStateRestorer();

        $userAfterRestore = $this->userService->fetchUser($user['user_id']);

        $this->assertEquals($userAfterRestore->getUserName(), $user['username']);
    }

    /**
     * @test
     */
    public function testItShouldRestoreDbStateForUserImages()
    {
        $this->userImageService->saveImageToUser('principal', 'math_student');
        $image = $this->userImageService->fetchImageForUser('math_student');
        $this->assertEquals('principal', $image->getImageId());

        $this->restoreService->runDbStateRestorer();

        try {
            $this->userImageService->fetchImageForUser('math_student');
            $this->fail("it did not reset image data");
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * @test
     */
    public function testItShouldRestoreStateForUserFlips()
    {
        $this->flipUserService->attachFlipToUser('other_student', 'polar-bear');

        $this->restoreService->runDbStateRestorer();

        $flipResultSet = $this->flipUserService->fetchEarnedFlipsForUser('other_student');

        $this->assertEquals($flipResultSet->count(), 0);
    }

    /**
     * @test
     */
    public function testItShouldRestoreStateForFriends()
    {
        $this->friendService->attachFriendToUser('english_student', 'math_student');
        $this->restoreService->runDbStateRestorer();
        try {
            $this->friendService->fetchFriendForUser('english_student', 'math_student');
            $this->fail("it did not reset friend data");
        } catch (NotFriendsException $nf) {
            //noop
        }
    }

    /**
     * @test
     */
    public function testItShouldRestoreStateForSuggestions()
    {
        $this->suggestedService->deleteSuggestionForUser('student', 'english_student');

        $this->restoreService->runDbStateRestorer();

        $this->suggestedService->fetchSuggestedFriendForUser('student', 'english_student');
    }

    /**
     * @test
     */
    public function testItShouldRestoreStateForSaveGames()
    {
        $date = new \DateTime();
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('monarch');
        $saveGame->setVersion('4.3.2.1');
        $saveGame->setCreated($date);
        $saveGame->setData(['baz' => 'bat']);
        $this->saveGameService->saveGame($saveGame);

        $this->restoreService->runDbStateRestorer();
        try {
            $this->saveGameService->fetchSaveGameForUser('english_student', 'monarch');
            $this->fail('it did not reset save game data');
        } catch (NotFoundException $nf) {
            //noop
        }
    }
}
