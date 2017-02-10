<?php

namespace IntegrationTest\Api\V1\Rest;

use Address\Address;
use Address\Service\AddressServiceInterface;
use Group\Service\GroupAddressServiceInterface;
use Application\Exception\NotFoundException;
use Group\Group;
use IntegrationTest\AbstractApigilityTestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Zend\Json\Json;

/**
 * Class GroupAddressResourceTest
 * @package IntegrationTest\Api\V1\Rest
 */
class GroupAddressResourceTest extends AbstractApigilityTestCase
{
    /**
     * @var GroupAddressServiceInterface
     */
    protected $groupAddressService;

    /**
     * @var AddressServiceInterface
     */
    protected $addressService;

    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../../../DataSets/AddressDataSet.php');
    }

    /**
     * @before
     */
    public function setUpServices()
    {
        $this->addressService = TestHelper::getDbServiceManager()->get(AddressServiceInterface::class);
        $this->groupAddressService = TestHelper::getDbServiceManager()->get(GroupAddressServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('super_user');
        $this->dispatch('/group/school/address');
        $this->assertResponseStatusCode(500);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');
    }

    /**
     * @test
     */
    public function testItShouldCheckIfUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/group/school/address');
        $this->assertResponseStatusCode(401);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');
    }

    /**
     * @test
     * @dataProvider userDataProvider
     */
    public function testItShouldCheckChangePasswordException($login)
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser($login);
        $this->dispatch('/group/school/address');
        $this->assertResponseStatusCode(401);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');
    }

    /**
     * @test
     */
    public function testItShouldNotFetchAddressIfUserHasNoAccessToGroup()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('principal');
        $this->dispatch('/group/other_school/address');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');
    }

    /**
     * @test
     */
    public function testItShouldNotFetchIfGroupIdIsInvalid()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('principal');
        $this->dispatch('/group/foo/address');
        $this->assertResponseStatusCode(403);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');
    }

    /**
     * @test
     * @dataProvider userDataProvider
     */
    public function testItShouldFetchAllAddressesForGroup($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/group/school/address');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');

        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('addresses', $body['_embedded']);

        $addresses = $body['_embedded']['addresses'];
        $actual = [];
        $expected = ['school_address'];

        foreach ($addresses as $address) {
            $this->assertArrayHasKey('address_id', $address);
            $actual[] = $address['address_id'];
        }

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider createDataProvider
     */
    public function testItShouldAttachAddressToGroup($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/group/english/address/school_address', 'POST');
        $this->assertResponseStatusCode(201);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');

        $group = new Group();
        $group->setGroupId('english');

        try {
            $this->groupAddressService->fetchAddressForGroup(
                $group,
                new Address(['address_id' => 'school_address'])
            );
        } catch (NotFoundException $nf) {
            $this->fail("it did not attach address correctly");
        }
    }

    /**
     * @test
     * @dataProvider createDataProvider
     */
    public function testItShouldDeleteGroupAddress($login)
    {
        $this->injectValidCsrfToken();
        $this->logInUser($login);
        $this->dispatch('/group/school/address/school_address', 'DELETE');
        $this->assertResponseStatusCode(200);
        $this->assertMatchedRouteName('api.rest.group-address');
        $this->assertControllerName('api\v1\rest\groupaddress\controller');

        $group = new Group();
        $group->setGroupId('english');

        try {
            $this->groupAddressService->fetchAddressForGroup(
                $group,
                new Address(['address_id' => 'school_address'])
            );
            $this->fail("It did not detach address from group");
        } catch (NotFoundException $nf) {
            //noop
        }
    }

    /**
     * return array
     */
    public function createDataProvider()
    {
        return [
            [
                'super_user'
            ],
            [
                'principal'
            ],
            [
                'english_teacher'
            ],
        ];
    }

    /**
     * return array
     */
    public function userDataProvider()
    {
        return [
            [
                'super_user'
            ],
            [
                'principal'
            ],
            [
                'english_teacher'
            ],
            [
                'english_student'
            ],
        ];
    }
}
