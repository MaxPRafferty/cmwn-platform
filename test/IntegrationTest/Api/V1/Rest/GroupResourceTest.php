<?php

namespace IntegrationTest\Api\V1\Rest;

use Application\Exception\NotFoundException;
use IntegrationTest\AbstractApigilityTestCase as TestCase;
use IntegrationTest\TestHelper;
use Group\Service\GroupServiceInterface;
use Zend\Json\Json;

/**
 * Test GroupResourceTest
 *
 * @group DB
 * @group Group
 * @group GroupService
 * @group Api
 * @group Integration
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupResourceTest extends TestCase
{
    /**
     * @test
     * @ticket core-864
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @test
     * @ticket CORE-993
     */
    public function testItShouldAllowPrincipalToAccessGroup()
    {
        $this->logInUser('principal');
        $this->injectValidCsrfToken();
        $this->dispatch('/group/english');

        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
    }

    /**
     * @test
     *
     * @param string $user
     * @param string $url
     * @param string $method
     * @param array $params
     *
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
    public function testToCheckIfUserIsLoggedInToAccessGroups()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/group');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testToCheckIfUserIsLoggedInToAccessAParticularGroup()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/group/school');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToReturnValidGroups()
    {
        $this->logInUser('english_student');

        $this->dispatch('/group');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     * @ticket CORE-864
     */
    public function testItShouldReturnValidGroups()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/group');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('group', $body['_embedded']);
        $groups      = $body['_embedded']['group'];
        $expectedIds = ['english', 'school'];
        $actualIds   = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actualIds[] = $group['group_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     * @ticket CORE-1060
     */
    public function testItShouldReturnValidGroupsForPrincipal()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('principal');
        $this->dispatch('/group');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('group', $body['_embedded']);
        $groups      = $body['_embedded']['group'];
        $expectedIds = ['english', 'school', 'math'];
        $actualIds   = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actualIds[] = $group['group_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToReturnSchoolForUser()
    {
        $this->logInUser('english_student');

        $this->dispatch('/group?type=school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     * @ticket       CORE-864
     * @ticket       CORE-725
     * @dataProvider schoolUserDataProvider
     */
    public function testItShouldReturnSchoolForUser($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/group?type=school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('group', $body['_embedded']);
        $groups      = $body['_embedded']['group'];
        $expectedIds = ['school'];
        $actualIds   = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actualIds[] = $group['group_id'];
        }
        $this->assertEquals($actualIds, $expectedIds);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToReturnGroupData()
    {
        $this->logInUser('english_student');

        $this->dispatch('/group/school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     * @dataProvider adultDataProvider
     */
    public function testItShouldReturnGroupDataForAdults($adult)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($adult);

        $this->dispatch('/group/school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('group_id', $body);
        $this->assertArrayHasKey('organization_id', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertEquals('school', $body['group_id']);
        $this->assertEquals('district', $body['organization_id']);
        $this->assertEquals('Gina\'s School', $body['title']);
    }

    /**
     * @test
     * @dataProvider childDataProvider
     */
    public function testItShouldReturnDenyGroupDataForChildren($child)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($child);

        $this->dispatch('/group/school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShould403WhenGroupWhichUserIsNotInIsAccessed()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/group/other_math');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider userDataProvider
     */
    public function testItShouldCheckWhenInvalidGroupIsAccessed($user, $code)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);

        $this->dispatch('/group/foobar');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode($code);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToCreateGroup()
    {
        $this->logInUser('super_user');

        $postData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => null,
        ];
        $this->dispatch('/group', POST, $postData);
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldCreateGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $postData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => ['code' => 'test'],
        ];
        $this->dispatch('/group', 'POST', $postData);
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(201);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('group_id', $body);
        $newGroup = $this->groupService->fetchGroup($body['group_id'])->getArrayCopy();
        $this->assertEquals('district', $newGroup['organization_id']);
        $this->assertEquals('Joni School', $newGroup['title']);
        $this->assertEquals('this is new school', $newGroup['description']);
        $this->assertEquals(['code' => 'test'], $newGroup['meta']);
    }

    /**
     * @test
     */
    public function testItShouldNotCreateGroupWithInvalidType()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $postData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'not-real',
            'meta'            => ['code' => 'test'],
        ];
        $this->dispatch('/group', 'POST', $postData);
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(422);
    }

    /**
     * @test
     */
    public function testItShouldNotAllowOthersToCreateGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $postData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => ['code' => 'test'],
        ];
        $this->dispatch('/group', 'POST', $postData);
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToDeleteGroup()
    {
        $this->logInUser('super_user');

        $this->dispatch('/group/school', 'DELETE');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldDeleteGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/group/school', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->setExpectedException(NotFoundException::class);
        $group = $this->groupService->fetchGroup('school')->getArrayCopy();
    }

    /**
     * @test
     */
    public function testItShouldNotAllowOthersToDeleteGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $this->dispatch('/group/school', 'DELETE');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToUpdateGroup()
    {
        $this->logInUser('super_user');

        $putData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => null,
        ];
        $this->dispatch('/group/school', 'PUT', $putData);
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldUpdateGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $putData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => null,
        ];
        $this->dispatch('/group/school', 'PUT', $putData);
        $this->assertResponseStatusCode(200);
        $group = $this->groupService->fetchGroup('school')->getArrayCopy();
        $this->assertEquals('district', $group['organization_id']);
        $this->assertEquals('Joni School', $group['title']);
        $this->assertEquals('this is new school', $group['description']);
        $this->assertEquals([], $group['meta']);
    }

    /**
     * @test
     */
    public function testItShouldNotAllowStudentToUpdateGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $putData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'type'            => 'school',
            'meta'            => null,
        ];
        $this->dispatch('/group/school', 'PUT', $putData);
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @test
     * @ticket CORE-1061
     */
    public function testItShouldNotLetPrincipalUpdateOtherGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('principal');

        $putData = [
            'organization_id' => 'district',
            'title'           => 'Joni School',
            'description'     => 'this is new school',
            'meta'            => null,
        ];
        $this->dispatch('/group/other_school', 'PUT', $putData);
        $this->assertResponseStatusCode(403);
    }

    /* @test
     * @ticket CORE-1062
     */
    public function testItShouldFetchChildGroups()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $this->dispatch('/group?type=class&parent=school');
        $this->assertMatchedRouteName('api.rest.group');
        $this->assertControllerName('api\v1\rest\group\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);

        $groupList = $body['_embedded'];
        $this->assertArrayHasKey('group', $groupList);

        $expectedGroupIds = [
            'english',
            'math',
        ];
        $actualGroupIds   = [];

        foreach ($groupList['group'] as $groupData) {
            $this->assertArrayHasKey('group_id', $groupData);
            array_push($actualGroupIds, $groupData['group_id']);
        }

        $this->assertEquals($expectedGroupIds, $actualGroupIds);
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/group',
            ],
            1 => [
                'super_user',
                '/group',
                'POST',
                [
                    'organization_id' => 'district',
                    'title'           => 'Joni School',
                    'description'     => 'this is new school',
                    'type'            => 'school',
                    'meta'            => null,
                ],
            ],
            2 => [
                'super_user',
                '/group/school',
                'PUT',
                [
                    'organization_id' => 'district',
                    'title'           => 'Joni School',
                    'description'     => 'this is new school',
                    'type'            => 'school',
                    'meta'            => null,
                ],
            ],
            3 => [
                'super_user',
                '/group/school',
                'DELETE',
            ],
            4 => [
                'english_student',
                '/group/school',
            ],
        ];
    }

    /**
     * @return array
     */
    public function schoolUserDataProvider()
    {
        return [
            'English Teacher' => [
                'english_teacher',
            ],
            'English Student' => [
                'english_student',
            ],
        ];
    }

    /**
     * @return array
     */
    public function userDataProvider()
    {
        return [
            'English student' => [
                'english_student',
                403,
            ],
            'English Teacher' => [
                'english_teacher',
                403,
            ],
        ];
    }

    /**
     * @return array
     */
    public function childDataProvider()
    {
        return [
            'English Student' => [
                'english_student',
            ],
            'Math Student' => [
                'math_student',
            ],
        ];
    }

    /**
     * @return array
     */
    public function adultDataProvider()
    {
        return [
            'English Teacher' => [
                'english_teacher',
            ],
            'Math Teacher' => [
                'math_teacher',
            ],
            'Principal' => [
                'principal',
            ],
            'Super' => [
                'super_user',
            ],
        ];
    }
}
