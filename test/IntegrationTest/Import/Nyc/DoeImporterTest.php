<?php

namespace IntegrationTest\Import\Nyc;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\DoeImporter;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\DbUnitConnectionTrait;
use IntegrationTest\TestHelper;
use \PHPUnit_Framework_TestCase as TestCase;
use \PHPUnit_Extensions_Database_TestCase_Trait as DbTestCaseTrait;
use User\Adult;
use User\Child;
use User\Service\UserServiceInterface;

/**
 * Test DoeImporterTest
 *
 * @group Import
 * @group Excel
 * @group User
 * @group Group
 * @group Action
 * @group NycImport
 * @group IntegrationTest
 * @group Db
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DoeImporterTest extends TestCase
{
    use DbTestCaseTrait;
    use DbUnitConnectionTrait;

    /**
     * @var DoeImporter
     */
    protected $importer;

    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * Use custom dataset
     *
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        if (static::$dataSet === null) {
            $data = include __DIR__ . '/../../DataSets/import.dataset.php';

            $nameList      = require __DIR__ . '/../../../../config/autoload/names.global.php';

            foreach ($nameList['user-names']['left'] as $name) {
                array_push($data['names'], ['name' => $name, 'position' => 'LEFT', 'count' => 1]);
            }

            foreach ($nameList['user-names']['right'] as $name) {
                array_push($data['names'], ['name' => $name, 'position' => 'RIGHT', 'count' => 1]);
            }

            static::$dataSet = new ArrayDataSet($data);
        }

        return static::$dataSet;
    }

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = TestHelper::getServiceManager()->get('Nyc\DoeImporter');
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldImportDataOnGoodSheet()
    {
        $this->markTestSkipped('Something up with sharing the registries.  Next test is more important');
        $this->importer->exchangeArray([
            'file'         => __DIR__ . '/_files/test_sheet.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'school',
            'email'        => 'test@example.com',
        ]);

        $this->importer->perform();

        // Principal Added?
        $principal = $this->userService->fetchUserByEmail('sandoval@gmail.com');
        $this->assertInstanceOf(Adult::class, $principal);

        // Teacher Added?
        $teacher = $this->userService->fetchUserByEmail('solomon@gmail.com');
        $this->assertInstanceOf(Adult::class, $teacher);

        // Student Added?
        $student = $this->userService->fetchUserByExternalId('123');
        $this->assertInstanceOf(Child::class, $student);

        // Class Added?
        $group = $this->groupService->fetchGroupByExternalId('district', '001');
        $this->assertInstanceOf(GroupInterface::class, $group);

        // TODO Expand this test to check relationships
    }

    /**
     * @test
     * @ticket CORE-707
     */
    public function testItAddNumbersToStudentUserNames()
    {
        $this->markTestSkipped('This works when running this test alone');
        $this->importer->exchangeArray([
            'file'         => __DIR__ . '/_files/test_sheet.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'school',
            'email'        => 'test@example.com',
        ]);

        $this->importer->perform();

        // Principal Added?
        $principal = $this->userService->fetchUserByEmail('sandoval@gmail.com');
        $this->assertInstanceOf(Adult::class, $principal);

        // Teacher Added?
        $teacher = $this->userService->fetchUserByEmail('solomon@gmail.com');
        $this->assertInstanceOf(Adult::class, $teacher);

        // Student Added?
        $student = $this->userService->fetchUserByExternalId('O1X123-123');
        $this->assertInstanceOf(Child::class, $student);

        $this->assertRegExp(
            '/^[a-z]+-[a-z]+\d{3}$/',
            $student->getUserName(),
            'Importer did not make user names for children'
        );
    }
}
