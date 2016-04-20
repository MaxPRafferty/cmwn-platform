<?php

namespace IntegrationTest\Service;

use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use Security\Service\SecurityGroupServiceInterface;
use User\StaticUserFactory;
use User\UserInterface;

/**
 * Exception UserGroupServiceTest
 * @group IntegrationTest
 */
class SecurityGroupServiceTest extends TestCase
{
    /**
     * @var SecurityGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet(__DIR__ . '/../DataSets/default.dataset.xml');
    }

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->userGroupService = TestHelper::getServiceManager()->get(SecurityGroupServiceInterface::class);
    }

    /**
     * @dataProvider relationshipsDataProvider
     */
    public function testItCorrectRelationships(array $activeUser, array $requestedUser, $expectedRole)
    {
        $activeUser    = StaticUserFactory::createUser($activeUser);
        $requestedUser = StaticUserFactory::createUser($requestedUser);

        $this->assertEquals(
            $expectedRole,
            $this->userGroupService->fetchRelationshipRole($activeUser, $requestedUser),
            'The role should be: ' . $expectedRole
        );
    }

    public function relationshipsDataProvider()
    {
        return [
            'English Teacher to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Math Teacher to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult'
            ],

            // English Student
            'English Teacher to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'teacher'
            ],

            'Math Teacher to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'guest'
            ],

            'Principal to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'principal'
            ],

            'Math Student to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'student'
            ],

            'English Student to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'me'
            ],
            
            // Math Student
            'English Teacher to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'guest'
            ],

            'Math Teacher to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'teacher'
            ],

            'Principal to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'principal'
            ],

            'Math Student to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'me'
            ],

            'English Student to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'student'
            ],

            // English Teacher
            'English Teacher to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'me'
            ],

            'Math Teacher to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Principal to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Math Student to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'guest'
            ],

            'English Student to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'student'
            ],

            // Math Teacher
            'English Teacher to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Math Teacher to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'me'
            ],

            'Principal to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Math Student to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'student'
            ],

            'English Student to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'guest'
            ],


            // Principal
            'English Teacher to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Math Teacher to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult'
            ],

            'Principal to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'me'
            ],

            'Math Student to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'student'
            ],

            'English Student to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'student'
            ],
        ];
    }
}
