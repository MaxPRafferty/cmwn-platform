<?php

namespace IntegrationTest\Api\V1\Rest;

use IntegrationTest\AbstractApigilityTestCase;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;
use Zend\Json\Json;

/**
 * IntegrationTest for AddressGroupResource
 */
class AddressGroupResourceTest extends AbstractApigilityTestCase
{
    /**
     * @inheritdoc
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../../../DataSets/AddressDataSet.php');
    }

    /**
     * @test
     */
    public function testItShouldCheckChangePasswordException()
    {
        $this->injectValidCsrfToken();
        $this->logInChangePasswordUser('super_user');
        $this->assertChangePasswordException('/address/foo_school_address/group');
    }

    /**
     * @test
     */
    public function testItShouldCheckIfTheUserIsLoggedIn()
    {
        $this->injectValidCsrfToken();
        $this->dispatch('/address/foo_school_address/group');
        $this->assertResponseStatusCode(401);
    }

    /**
     * @test
     */
    public function testItShouldCheckCsrf()
    {
        $this->logInUser('super_user');
        $this->dispatch('/address/foo_school_address/group');
        $this->assertResponseStatusCode(500);
    }
    /**
     * @test
     */
    public function testItShouldFetchAllGroupsInTheAddress()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('super_user');
        $this->dispatch('/address/foo_school_address/group');
        $this->assertResponseStatusCode(200);
        $body = Json::decode($this->getResponse()->getContent(), Json::TYPE_ARRAY);
        $this->assertArrayHasKey('_embedded', $body);
        $this->assertArrayHasKey('groups', $body['_embedded']);
        $groups = $body['_embedded']['groups'];
        $expected = ['foo_school'];
        $actual = [];
        foreach ($groups as $group) {
            $this->assertArrayHasKey('group_id', $group);
            $actual[] = $group['group_id'];
        }
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function testItShould403ForOthers()
    {
        $this->injectValidCsrfToken();
        $this->logInUser('english_student');
        $this->dispatch('/address/foo_school_address/group');
        $this->assertResponseStatusCode(403);
    }
}
