<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Adult;
use User\Service\UserServiceInterface;
use User\StaticUserFactory;
use Zend\Json\Json;

/**
 * Test OrgUsersResourceTest
 *
 * @group IntegrationTest
 * @group Api
 * @group Org
 * @group UserGroupService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrgUsersResourceTest extends TestCase
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @return \PHPUnit\DbUnit\DataSet\ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/org.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
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
     * @ticket CORE-610
     * @dataProvider organizationUsersDataProvider
     */
    public function testItShouldUsersForUsersForOrganization($login, $orgId, array $expectedIds)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/org/' . $orgId . '/users');

        $this->assertResponseStatusCode(200);
        $this->assertNotRedirect();
        $this->assertMatchedRouteName('api.rest.org-users');
        $this->assertControllerName('api\v1\rest\orgusers\controller');

        $body = $this->getResponse()->getContent();

        try {
            $decoded = Json::decode($body, Json::TYPE_ARRAY);
        } catch (\Exception $jsonException) {
            $this->fail('Error Decoding Response');
            return;
        }

        $this->assertArrayHasKey(
            '_embedded',
            $decoded,
            'Invalid Response from API;'
        );

        $embedded = $decoded['_embedded'];
        $this->assertArrayHasKey('items', $embedded, 'Embedded does not contain any users');

        $actualIds = [];
        foreach ($embedded['items'] as $user) {
            $actualUser  = StaticUserFactory::createUser($user);
            $actualIds[] = $actualUser->getUserId();
        }

        sort($expectedIds);
        sort($actualIds);
        $this->assertEquals($expectedIds, $actualIds, 'Api Did not Return Correct Users for: ' . $login);
    }

    /**
     * @test
     * @ticket CORE-610
     * @dataProvider deletedOrganizationUsersDataProvider
     */
    public function testItShouldReturnBackCorrectUsersWhenDeleted($login, $orgId, $deleteUser, array $expectedIds)
    {
        $user = new Adult();
        $user->setUserId($deleteUser);
        $this->assertTrue($this->userService->deleteUser($user));
        $this->testItShouldUsersForUsersForOrganization($login, $orgId, $expectedIds);
    }

    /**
     * @return array
     */
    public function organizationUsersDataProvider()
    {
        return [
            'Super User' => [
                'Active User'    => 'super_user',
                'Organization'   => 'district',
                'Expected Users' => [
                    'english_student',
                    'english_teacher',
                    'math_student',
                    'math_teacher',
                    'principal',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function deletedOrganizationUsersDataProvider()
    {
        return [
            'Super User' => [
                'Active User'    => 'super_user',
                'Organization'   => 'district',
                'Delete User'    => 'math_teacher',
                'Expected Users' => [
                    'english_student',
                    'english_teacher',
                    'math_student',
                    'principal',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
                '/org/district/users'
            ],
        ];
    }
}
