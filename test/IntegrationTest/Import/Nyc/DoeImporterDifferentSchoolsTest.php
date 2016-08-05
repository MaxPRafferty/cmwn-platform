<?php

namespace IntegrationTest\Import\Nyc;

use Group\Group;
use Group\GroupInterface;
use Import\Importer\Nyc\DoeImporter;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use Security\Authentication\AuthenticationService;
use Security\Service\SecurityService;
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

        return new ArrayDataSet($data);
    }

    /**
     * @before
     */
    public function setUpLoggedInUser()
    {
        /** @var SecurityService $userService */
        $userService = TestHelper::getServiceManager()->get(SecurityService::class);
        $user        = $userService->fetchUserByUserName('super_user');
        $authService = TestHelper::getServiceManager()->get(AuthenticationService::class);
        $authService->getStorage()->write($user);
    }

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = NycDoeTestImporterSetup::getImporter();
        $this->importer->exchangeArray([
            'file'         => __DIR__ . '/_files/hogwarts.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'hogwarts',
            'email'        => 'test@example.com',
        ]);

        $this->importer->setLogger(new Logger(['writers' => [['name' => 'noop']]]));
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
        $this->assertEmpty($this->importer->perform());
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
        $groups = new Paginator($this->getGroupService()->fetchAll(null, true, new Group));

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
        $userGroups = new Paginator($this->getUserGroupService()->fetchGroupsForUser('english_student'), new Group());

        $expectedGroupNames = [
            'English Class',
            'Gina\'s School',
        ];
        $actualGroupNames   = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group['title'];
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
        $userGroups = new Paginator($this->getUserGroupService()->fetchGroupsForUser('math_student'), new Group());

        $expectedGroupNames = [
            'Gina\'s School',
            'Math Class',
        ];
        $actualGroupNames   = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group['title'];
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
        $padma      = $this->getUserService()->fetchUserByExternalId('01C123-0001');
        $userGroups = new Paginator($this->getUserGroupService()->fetchGroupsForUser($padma));

        $expectedGroupNames = [
            'History of Magic',
            'Hogwarts',
        ];

        $actualGroupNames = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group['title'];
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
        $padma      = $this->getUserService()->fetchUserByExternalId('01C123-0002');
        $userGroups = new Paginator($this->getUserGroupService()->fetchGroupsForUser($padma));

        $expectedGroupNames = [
            'History of Magic',
            'Hogwarts',
        ];

        $actualGroupNames = [];

        foreach ($userGroups as $group) {
            $actualGroupNames[] = $group['title'];
        }

        $this->assertEquals(
            $expectedGroupNames,
            $actualGroupNames,
            'Lee was not assigned to the correct groups'
        );

        return $this;
    }
}