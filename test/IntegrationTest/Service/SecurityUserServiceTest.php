<?php

namespace IntegrationTest\Service;

use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use Security\Service\SecurityUserService;
use Security\Service\SecurityUserServiceInterface;
use User\StaticUserFactory;
use User\UserInterface;

/**
 * Exception UserGroupServiceTest
 *
 * @group Security
 * @group Group
 * @group UserGroup
 * @group IntegrationTest
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SecurityUserServiceTest extends TestCase
{
    /**
     * @var SecurityUserService
     */
    protected $securityUserService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/group.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->securityUserService = TestHelper::getServiceManager()->get(SecurityUserServiceInterface::class);
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
            $this->securityUserService->fetchRelationshipRole($activeUser, $requestedUser),
            'The role should be: ' . $expectedRole
        );
    }

    public function relationshipsDataProvider()
    {
        return [
            // English Student
            'English Teacher to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'teacher.adult',
            ],

            'Math Teacher to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'guest',
            ],

            'Principal to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'principal.adult',
            ],

            'Math Student to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'student.child',
            ],

            'English Student to English Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'expectedRole'  => 'me.child',
            ],

            // Math Student
            'English Teacher to Math Student'    => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'guest',
            ],

            'Math Teacher to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'teacher.adult',
            ],

            'Principal to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'principal.adult',
            ],

            'Math Student to Math Student' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'me.child',
            ],

            'English Student to Math Student'    => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'expectedRole'  => 'student.child',
            ],

            // English Teacher
            'English Teacher to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'me.adult',
            ],

            'Math Teacher to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'neighbor.adult',
            ],

            'Principal to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'principal.adult',
            ],

            'Math Student to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'guest',
            ],

            'English Student to English Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'expectedRole'  => 'student.child',
            ],

            // Math Teacher
            'English Teacher to Math Teacher'    => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'neighbor.adult',
            ],

            'Math Teacher to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'me.adult',
            ],

            'Principal to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'principal.adult',
            ],

            'Math Student to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'student.child',
            ],

            'English Student to Math Teacher' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'expectedRole'  => 'guest',
            ],

            // Principal
            'English Teacher to Principal'    => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'english_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult',
            ],

            'Math Teacher to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'math_teacher'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'neighbor.adult',
            ],

            'Principal to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'me.adult',
            ],

            'Math Student to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'math_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'student.child',
            ],

            'English Student to Principal' => [
                'activeUser'    => ['type' => UserInterface::TYPE_CHILD, 'user_id' => 'english_student'],
                'requestedUser' => ['type' => UserInterface::TYPE_ADULT, 'user_id' => 'principal'],
                'expectedRole'  => 'student.child',
            ],
        ];
    }
}
