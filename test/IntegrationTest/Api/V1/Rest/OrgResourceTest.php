<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase as TestCase;
use Security\Authorization\Rbac;
use Zend\Json\Json;
use IntegrationTest\TestHelper;
use Org\Service\OrganizationServiceInterface;
use Application\Exception\NotFoundException;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Test OrgResourceTest
 * @group Org
 * @group DB
 * @group API
 * @group Integration
 * @group OrgService
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OrgResourceTest extends TestCase
{
    /**
     * @var OrganizationServiceInterface
     */
    protected $orgService;

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/org.dataset.php');
    }

    /**
     * @before
     */
    public function setUpOrgService()
    {
        $this->orgService = TestHelper::getServiceManager()->get(OrganizationServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpRbac()
    {
        $this->rbac = TestHelper::getServiceManager()->get(Rbac::class);
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
    public function testItShouldCheckIfUserLoggedIn()
    {
        $this->injectValidCsrfToken();

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('english_student');

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     */
    public function testItShouldFetchOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $this->dispatch('/org/district');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('org_id', $body);
        $this->assertArrayHasKey('title', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('type', $body);
        $this->assertEquals('district', $body['org_id']);
        $this->assertEquals('Gina\'s District', $body['title']);
        $this->assertEquals('district', $body['type']);
        $this->assertEquals(null, $body['description']);
    }

    /**
     * @test
     * @ticket CORE-1184
     */
    public function testItShouldDenyFetchOrgForChild()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/org/district');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider userDataProvider
     */
    public function testItShouldCheckFetchOrgOnInvalidOrg($user, $code)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($user);

        $this->dispatch('/org/foo');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode($code);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/org');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('org', $body['_embedded']);
        $expectedIds = ['district', 'manchuck'];
        foreach ($body['_embedded']['org'] as $org) {
            $this->assertArrayHasKey('org_id', $org);
            $actualIds[] = $org['org_id'];
        }
        $this->assertEquals($expectedIds, $actualIds);
    }

    /**
     * @test
     * @ticket CORE-884
     */
    public function testItShould403WhenUserFetchOtherOrg()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('math_student');

        $this->dispatch('/org/manchuck');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-1185
     */
    public function testItShouldFetchAllOrgsAnAdultBelongToo()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_teacher');

        $this->dispatch('/org');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('org', $body['_embedded']);
        $orgs = $body['_embedded']['org'];
        $expected = ['district'];
        $actual = [];
        foreach ($orgs as $org) {
            $actual[] = $org['org_id'];
        }

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     * @ticket CORE-1185
     */
    public function testItShouldDenyFetchAllOrgsForChildren()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');

        $this->dispatch('/org');
        $this->assertMatchedRouteName('api.rest.org');
        $this->assertControllerName('api\v1\rest\org\controller');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldCreateOrganization()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch(
            '/org',
            'POST',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(201);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('org_id', $body);

        $newOrg = $this->orgService->fetchOrganization($body['org_id'])->getArrayCopy();
        $this->assertEquals('newOrg', $newOrg['title']);
        $this->assertEquals('new organization', $newOrg['description']);
        $this->assertEquals('district', $newOrg['type']);
        $this->assertEquals([], $newOrg['meta']);
    }

    /**
     * @test
     * @dataProvider adultDataProvider
     */
    public function testItShouldDenyCreateOrganizationForAdults($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch(
            '/org',
            'POST',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @dataProvider childDataProvider
     */
    public function testItShouldDenyCreateOrganizationForChildren($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch(
            '/org',
            'POST',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrfToCreateOrganization()
    {
        $this->logInUser('super_user');

        $this->dispatch(
            '/org',
            'POST',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(500);
    }

    /**
     * @test
     * @ticket CORE-885
     */
    public function testItShouldDeleteOrganization()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch('/org/district', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->setExpectedException(NotFoundException::class);
        $this->orgService->fetchOrganization('district')->getArrayCopy();
    }

    /**
     * @test
     * @ticket CORE-1184
     * @dataProvider adultDataProvider
     */
    public function testItShouldDenyDeleteOrganizationForAdult($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/org/district', 'DELETE');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-1184
     * @dataProvider childDataProvider
     */
    public function testItShouldDenyDeleteOrganizationForChildren($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch('/org/district', 'DELETE');
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-886
     */
    public function testItShouldUpdateOrganization()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');

        $this->dispatch(
            '/org/district',
            'PUT',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(200);

        $newOrg = $this->orgService->fetchOrganization('district')->getArrayCopy();
        $this->assertEquals('newOrg', $newOrg['title']);
        $this->assertEquals('new organization', $newOrg['description']);
        $this->assertEquals('district', $newOrg['type']);
        $this->assertEquals([], $newOrg['meta']);
    }

    /**
     * @test
     * @ticket CORE-1184
     * @dataProvider adultDataProvider
     */
    public function testItShouldDenyUpdateOrganizationForAdults($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch(
            '/org/district',
            'PUT',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-1184
     * @dataProvider childDataProvider
     */
    public function testItShouldDenyUpdateOrganizationForChildren($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);

        $this->dispatch(
            '/org/district',
            'PUT',
            [
                'title' => 'newOrg',
                'description' => 'new organization',
                'type' => 'district',
                'meta' => null,
            ]
        );
        $this->assertResponseStatusCode(403);
    }

    /**
     * @test
     * @ticket CORE-2525
     * @group CORE-2525
     */
    public function testItShouldNotShowUserOrgLinkWhenUserDoesNotHavePermissionToViewOrgUsers()
    {
        $this->injectValidCsrfToken();
        $user = $this->logInUser('principal');

        $this->assertFalse(
            $this->rbac->isGranted($user->getRole(), 'view.org.users'),
            'This test cannot run with a user that has permission to view.org.users'
        );

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);
        $this->assertArrayNotHasKey(
            'org_users',
            $body['_links'],
            'User with permission for view.org.users was given the HAL link for org_users'
        );
    }

    /**
     * @test
     * @ticket CORE-2525
     * @group CORE-2525
     */
    public function testItShouldShowUserOrgLinkWhenUserHasPermissionToViewOrgUsers()
    {
        $this->injectValidCsrfToken();
        $user = $this->logInUser('super_user');

        $this->assertTrue(
            $this->rbac->isGranted($user->getRole(), 'view.org.users'),
            'This test cannot run with a user that does not permission to view.org.users'
        );

        $this->dispatch('/org/district');
        $this->assertResponseStatusCode(200);

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_links', $body);
        $this->assertArrayHasKey(
            'org_users',
            $body['_links'],
            'User with permission for view.org.users was NOT given the HAL link for org_users'
        );
    }

    /**
     * @return array
     */
    public function changePasswordDataProvider()
    {
        return [
            0 => [
                'english_student',
                '/org'
            ],
            1 => [
                'super_user',
                '/org',
                'POST',
                [
                    'title' => 'newOrg',
                    'description' => 'new organization',
                    'type' => 'district',
                    'meta' => null,
                ]
            ],
            2 => [
                'super_user',
                '/org/district',
                'DELETE'
            ],
            3 => [
                'super_user',
                '/org/district',
                'PUT',
                [
                    'title' => 'newOrg',
                    'description' => 'new organization',
                    'type' => 'district',
                    'meta' => null,
                ]
            ],
            4 => [
                'english_student',
                '/org/district'
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
                403
            ],
            'English Teacher' => [
                'english_teacher',
                403
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
        ];
    }
}
