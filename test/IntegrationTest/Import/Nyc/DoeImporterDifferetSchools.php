<?php

namespace IntegrationTest\Import\Nyc;

use Group\Group;
use Import\Importer\Nyc\DoeImporter;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
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
class DoeImporterDifferetSchools extends TestCase
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
        $this->checkEnglishStudent()
            ->checkMathStudent()
            ->checkPadma()
            ->checkLee();
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
        $padma = $this->getUserService()->fetchUserByExternalId('01C123-0001');
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
        $padma = $this->getUserService()->fetchUserByExternalId('01C123-0002');
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
