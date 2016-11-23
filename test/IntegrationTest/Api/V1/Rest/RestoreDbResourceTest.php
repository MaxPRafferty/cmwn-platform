<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use Application\Exception\PreConditionFailedException;
use Asset\Service\UserImageServiceInterface;
use Flip\Service\FlipUserServiceInterface;
use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Game\SaveGame;
use Game\Service\SaveGameServiceInterface;
use Group\Service\GroupServiceInterface;
use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Org\Service\OrganizationServiceInterface;
use Suggest\Service\SuggestedServiceInterface;
use Suggest\Suggestion;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Class RestoreDbResourceTest
 * @package IntegrationTest\Api\V1\Rest
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RestoreDbResourceTest extends AbstractApigilityTestCase
{
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
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
        $this->userImageService = TestHelper::getServiceManager()->get(UserImageServiceInterface::class);
        $this->flipUserService = TestHelper::getServiceManager()->get(FlipUserServiceInterface::class);
        $this->friendService = TestHelper::getServiceManager()->get(FriendServiceInterface::class);
        $this->saveGameService = TestHelper::getServiceManager()->get(SaveGameServiceInterface::class);
        $this->suggestedService = TestHelper::getServiceManager()->get(SuggestedServiceInterface::class);
        $this->groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
        $this->orgService = TestHelper::getServiceManager()->get(OrganizationServiceInterface::class);
        $config = TestHelper::getServiceManager()->get('config');
    }

    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        $dataSet = include __DIR__ . '/../../../DataSets/restore.dataset.php';
        return new ArrayDataSet($dataSet);
    }

    /**
     * Helper function to set the test environment
     */
    protected function initializer()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
    }
    
    /**
     * @test
     */
    public function testItShouldNotAllowUnauthorizedUsersToRestoreDb()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldNotRestoreDbWhenFlagNotSet()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $statusCode = 200;

        $this->dispatch('/restore');
        $this->assertResponseStatusCode($statusCode);
    }

    /**
     * @test
     */
    public function testItShouldRestoreDbStateForTestUsers()
    {
        $this->initializer();

        $user = $this->userService->fetchUser('english_student');
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Yoder', $user->getLastName());

        $this->userService->updateUser(
            new Child([
                'user_id' => 'english_student',
                'first_name' => 'foo',
                'last_name' => 'bar',
            ])
        );

        $userBefore = $this->userService->fetchUser('english_student');
        $this->assertEquals('foo', $userBefore->getFirstName());
        $this->assertEquals('bar', $userBefore->getLastName());

        $this->dispatch('/Restore');

        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $userAfter = $this->userService->fetchUser('english_student');
        $this->assertEquals('John', $userAfter->getFirstName());
        $this->assertEquals('Yoder', $userAfter->getLastName());
    }
    
    /**
     * @test
     */
    public function testItShouldRestoreDbForTestImages()
    {
        $this->initializer();

        try {
            $this->userImageService->fetchImageForUser('math_student');
            $this->fail('math_student should not have image for this test to pass');
        } catch (NotFoundException $nf) {
            $this->userImageService->saveImageToUser('principal', 'math_student');
            $image = $this->userImageService->fetchImageForUser('math_student');
            $this->assertEquals('principal', $image->getImageId());

            $this->dispatch('/Restore');

            try {
                $this->userImageService->fetchImageForUser('math_student');
                $this->fail('it did not Restore user_images');
            } catch (NotFoundException $notFound) {
                //noop
            }

            $this->assertControllerName('api\v1\rest\restoredb\controller');
            $this->assertMatchedRouteName('api.rest.Restore');
            $this->assertResponseStatusCode(200);
        }
    }
    
    /**
     * @test
     */
    public function testItShouldRestoreDbForTestFriends()
    {
        $this->initializer();

        try {
            $this->friendService->fetchFriendForUser('english_student', 'math_student');
        } catch (NotFriendsException $nf) {
            $this->friendService->attachFriendToUser('english_student', 'math_student');

            $this->friendService->fetchFriendForUser('english_student', 'math_student');

            $this->dispatch('/Restore');

            $this->assertControllerName('api\v1\rest\restoredb\controller');
            $this->assertMatchedRouteName('api.rest.Restore');
            $this->assertResponseStatusCode(200);

            try {
                $this->friendService->fetchFriendForUser('english_student', 'math_student');
                $this->fail('it did not Restore friends table');
            } catch (NotFriendsException $notFriend) {
                //noop
            }
        }
    }

    /**
     * @test
     */
    public function testItShouldRestoreDbForTestFlips()
    {
        $this->initializer();

        $resultSet = $this->flipUserService->fetchEarnedFlipsForUser('other_student');
        $this->assertEquals($resultSet->count(), 0);
        $this->flipUserService->attachFlipToUser('other_student', 'polar-bear');
        $resultSet = $this->flipUserService->fetchEarnedFlipsForUser('other_student');
        $this->assertEquals($resultSet->count(), 1);

        $this->dispatch('/Restore');

        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $resultSet = $this->flipUserService->fetchEarnedFlipsForUser('other_student');
        $this->assertEquals($resultSet->count(), 0);
    }
    
    /**
     * @test
     */
    public function testItShouldRestoreDbForTestGames()
    {
        $this->initializer();

        $resultSet = $this->saveGameService->fetchAllSaveGamesForUser('english_student');
        $this->assertEquals(0, $resultSet->count());

        $date = new \DateTime();
        $saveGame = new SaveGame();
        $saveGame->setUserId('english_student');
        $saveGame->setGameId('monarch');
        $saveGame->setVersion('4.3.2.1');
        $saveGame->setCreated($date);
        $saveGame->setData(['baz' => 'bat']);
        $this->saveGameService->saveGame($saveGame);

        $resultSet = $this->saveGameService->fetchAllSaveGamesForUser('english_student');
        $this->assertEquals(1, $resultSet->count());

        $this->dispatch('/Restore');

        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $resultSet = $this->saveGameService->fetchAllSaveGamesForUser('english_student');
        $this->assertEquals(0, $resultSet->count());
    }
    
    /**
     * @test
     */
    public function testItShouldRestoreDbForTestSuggestions()
    {
        $this->initializer();

        try {
            $this->suggestedService->fetchSuggestedFriendForUser('english_student', 'math_student');
            $this->fail('math student should not be a suggested friend for english student for this test to run');
        } catch (\Suggest\NotFoundException $nf) {
            $this->suggestedService->attachSuggestedFriendForUser('english_student', 'math_student');
            $suggestion = $this->suggestedService->fetchSuggestedFriendForUser('english_student', 'math_student');
            $this->assertInstanceOf(Suggestion::class, $suggestion);
            $this->assertEquals($suggestion->getUserId(), 'math_student');

            $this->dispatch('/Restore');

            $this->assertControllerName('api\v1\rest\restoredb\controller');
            $this->assertMatchedRouteName('api.rest.Restore');
            $this->assertResponseStatusCode(200);

            try {
                $this->suggestedService->fetchSuggestedFriendForUser('english_student', 'math_student');
                $this->fail('it did not Restore db for suggestions');
            } catch (\Suggest\NotFoundException $notFound) {
                //noop
            }
        }
    }

    /**
     * @test
     */
    public function testItShouldNotModifyActualUsersWhileRestoring()
    {
        $this->initializer();

        $beforeUser = $this->userService->fetchUser('chaithra');

        $this->dispatch('/Restore');

        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterUser = $this->userService->fetchUser('chaithra');

        $this->assertEquals($beforeUser, $afterUser);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyActualGroupsWhileRestoring()
    {
        $this->initializer();

        $beforeGroup = $this->groupService->fetchGroup('chaithra_school');

        $this->dispatch('/Restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterGroup = $this->groupService->fetchGroup('chaithra_school');

        $this->assertEquals($beforeGroup, $afterGroup);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyActualOrganizationsWhileRestoring()
    {
        $this->initializer();

        $beforeOrg = $this->orgService->fetchOrganization('chaithra');

        $this->dispatch('/Restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterOrg = $this->orgService->fetchOrganization('chaithra');

        $this->assertEquals($beforeOrg, $afterOrg);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyExistingImages()
    {
        $this->initializer();

        $beforeImage = $this->userImageService->fetchImageForUser('chaithra', 'english_approved');

        $this->dispatch('/Restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterImage = $this->userImageService->fetchImageForUser('chaithra', 'english_approved');

        $this->assertEquals($beforeImage, $afterImage);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyExistingFlips()
    {
        $this->initializer();

        $beforeFlips = $this->flipUserService->fetchEarnedFlipsForUser('chaithra');
        $beforeFlips = $beforeFlips->getItems(0, $beforeFlips->count());

        $this->dispatch('/Restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterFlips = $this->flipUserService->fetchEarnedFlipsForUser('chaithra');
        $afterFlips = $afterFlips->getItems(0, $afterFlips->count());

        $this->assertEquals($beforeFlips, $afterFlips);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyExistingSuggestions()
    {
        $this->initializer();

        $beforeSuggestions = $this->suggestedService->fetchSuggestedFriendForUser('chaithra', 'foo_bar');

        $this->dispatch('/Restore');
        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterSuggestions = $this->suggestedService->fetchSuggestedFriendForUser('chaithra', 'foo_bar');

        $this->assertEquals($beforeSuggestions, $afterSuggestions);
    }

    /**
     * @test
     */
    public function testItShouldNotModifyExistingSaveGames()
    {
        $this->initializer();

        $beforeSaveGames = $this->saveGameService->fetchAllSaveGamesForUser('chaithra');
        $beforeSaveGames = $beforeSaveGames->getItems(0, $beforeSaveGames->count());

        $this->dispatch('/Restore');

        $this->assertControllerName('api\v1\rest\restoredb\controller');
        $this->assertMatchedRouteName('api.rest.Restore');
        $this->assertResponseStatusCode(200);

        $afterSaveGames = $this->saveGameService->fetchAllSaveGamesForUser('chaithra');
        $afterSaveGames = $afterSaveGames->getItems(0, $afterSaveGames->count());

        $this->assertEquals($beforeSaveGames, $afterSaveGames);
    }
}
