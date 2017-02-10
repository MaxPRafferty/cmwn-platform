<?php

namespace IntegrationTest\Import\Nyc;

use Group\GroupInterface;
use Import\Importer\Nyc\DoeImporter;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\Authentication\AuthenticationService;
use Security\Service\SecurityService;
use User\Child;
use Zend\Log\Logger;
use Zend\Paginator\Paginator;

/**
 * Test DoeImporterDifferetSchools
 *
 * @group IntegraionTest
 * @group Db
 * @group Import
 * @group Nyc
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DoeImporterDifferentSchoolsTest extends TestCase
{
    /**
     * @var DoeImporter
     */
    protected $importer;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        $data = include __DIR__ . '/../../DataSets/duplicate.import.dataset.php';

        return $this->createArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpLoggedInUser()
    {
        /** @var SecurityService $userService */
        $userService = TestHelper::getDbServiceManager()->get(SecurityService::class);
        $user        = $userService->fetchUserByUserName('super_user');
        $authService = TestHelper::getDbServiceManager()->get(AuthenticationService::class);
        $authService->getStorage()->write($user);
    }

    /**
     * @return DoeImporter
     */
    public function getImporter()
    {
        $importer = NycDoeTestImporterSetup::getImporter();
        $importer->exchangeArray([
            'file'         => __DIR__ . '/_files/hogwarts.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'hogwarts',
            'email'        => 'test@example.com',
        ]);

        $importer->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
        return $importer;
    }

    /**
     * @return \User\Service\UserServiceInterface
     */
    public function getUserService()
    {
        return NycDoeTestImporterSetup::getUserService();
    }

    /**
     * @return \Group\Service\UserGroupServiceInterface
     */
    public function getUserGroupService()
    {
        return NycDoeTestImporterSetup::getUserGroupService();
    }

    /**
     * @return \Group\Service\GroupServiceInterface
     */
    public function getGroupService()
    {
        return NycDoeTestImporterSetup::getGroupService();
    }

    /**
     * @test
     * @ticket CORE-864
     */
    public function testItShouldNotAssignStudentsToClassesThatShareStudentIdWithStudentsFromOtherDistrictsAndSchool()
    {
        $this->assertEmpty($this->getImporter()->perform());
        $this->checkAssociations();
    }

    protected function checkAssociations()
    {
        $this
            ->checkClasses()
            ->checkEnglishStudent()
            ->checkMathStudent()
            ->checkPadma()
            ->checkLee();
    }

    /**
     * @return $this
     */
    protected function checkClasses()
    {
        $groups = new Paginator($this->getGroupService()->fetchAll());

        $expectedGroups = [
            [
                'title'           => 'English Class',
                'organization_id' => 'district',
                'type'            => 'class',
                'external_id'     => '789',
            ],
            [
                'title'           => 'Gina\'s School',
                'organization_id' => 'district',
                'type'            => 'school',
                'external_id'     => null,
            ],
            [
                'title'           => 'Herbology',
                'organization_id' => 'm-o-m',
                'type'            => 'class',
                'external_id'     => '789',
            ],
            [
                'title'           => 'History of Magic',
                'organization_id' => 'm-o-m',
                'type'            => 'class',
                'external_id'     => '123',
            ],
            [
                'title'           => 'Hogwarts',
                'organization_id' => 'm-o-m',
                'type'            => 'school',
                'external_id'     => null,
            ],
            [
                'title'           => 'Math Class',
                'organization_id' => 'district',
                'type'            => 'class',
                'external_id'     => '456',
            ],
            [
                'title'           => 'Potions',
                'organization_id' => 'm-o-m',
                'type'            => 'class',
                'external_id'     => '456',
            ],
        ];

        $actualGroups   = [];
        foreach ($groups as $group) {
            /** @var GroupInterface $group */
            $actualGroups[] = [
                'title'           => $group->getTitle(),
                'organization_id' => $group->getOrganizationId(),
                'type'            => $group->getType(),
                'external_id'     => $group->getExternalId(),
            ];
        }

        $this->assertEquals(
            $expectedGroups,
            $actualGroups,
            'Importer did not create the correct groups'
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkEnglishStudent()
    {
        $user = new Child();
        $user->setUserId('english_student');
        $userGroups = $this->getUserGroupService()->fetchGroupsForUser($user)->getItems(0, 100);

        $expectedGroupNames = [
            'English Class',
            'Gina\'s School',
        ];
        $actualGroupNames   = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group->getTitle();
        }

        $this->assertEquals(
            $expectedGroupNames,
            $actualGroupNames,
            'English Student was reassigned to incorrect groups'
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkMathStudent()
    {
        $user = new Child();
        $user->setUserId('math_student');
        $userGroups = $this->getUserGroupService()->fetchGroupsForUser($user)->getItems(0, 100);


        $expectedGroupNames = [
            'Gina\'s School',
            'Math Class',
        ];
        $actualGroupNames   = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group->getTitle();
        }

        $this->assertEquals(
            $expectedGroupNames,
            $actualGroupNames,
            'Math Student was reassigned to incorrect groups'
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkPadma()
    {
        $user      = $this->getUserService()->fetchUserByExternalId('01C123-0001');
        $userGroups = $this->getUserGroupService()->fetchGroupsForUser($user)->getItems(0, 100);

        $expectedGroupNames = [
            'History of Magic',
            'Hogwarts',
        ];

        $actualGroupNames = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group->getTitle();
        }

        $this->assertEquals(
            $expectedGroupNames,
            $actualGroupNames,
            'Padma was not assigned to the correct groups'
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function checkLee()
    {
        $user      = $this->getUserService()->fetchUserByExternalId('01C123-0002');
        $userGroups = $this->getUserGroupService()->fetchGroupsForUser($user)->getItems(0, 100);

        $expectedGroupNames = [
            'History of Magic',
            'Hogwarts',
        ];

        $actualGroupNames = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group->getTitle();
        }

        $this->assertEquals(
            $expectedGroupNames,
            $actualGroupNames,
            'Lee was not assigned to the correct groups'
        );

        return $this;
    }
}
