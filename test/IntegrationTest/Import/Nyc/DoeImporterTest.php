<?php

namespace IntegrationTest\Import\Nyc;

use Group\GroupInterface;
use Group\Service\GroupServiceInterface;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\DbUnitConnectionTrait;
use IntegrationTest\LoginUserTrait;
use IntegrationTest\TestHelper;
use PHPUnit\DbUnit\TestCase as TestCase;
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
    use DbUnitConnectionTrait;
    use LoginUserTrait;

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

            static::$dataSet = $this->createArrayDataSet($data);
        }

        return static::$dataSet;
    }

    /**
     * @before
     */
    public function setUpImporter()
    {
        $this->importer = TestHelper::getDbServiceManager()->get('Nyc\DoeImporter');
    }

    /**
     * @before
     */
    public function setUpUserService()
    {
        $this->userService = TestHelper::getDbServiceManager()->get(UserServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpGroupService()
    {
        $this->groupService = TestHelper::getDbServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldImportDataOnGoodSheet()
    {
        $this->logInUser('super_user');
        $importer = NycDoeTestImporterSetup::getImporter();
        $importer->exchangeArray([
            'file'         => __DIR__ . '/_files/test_sheet.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'school',
            'email'        => 'test@example.com',
        ]);

        $importer->perform();

        // Principal Added?
        $principal = $this->userService->fetchUserByEmail('sandoval@gmail.com');
        $this->assertInstanceOf(Adult::class, $principal);

        // Teacher Added?
        $teacher = $this->userService->fetchUserByEmail('solomon@gmail.com');
        $this->assertInstanceOf(Adult::class, $teacher);

        // Student Added?
        $student = $this->userService->fetchUserByExternalId('01X100-123');
        $this->assertInstanceOf(Child::class, $student);

        // Class Added?
        $group = $this->groupService->fetchGroupByExternalId('school', '001');
        $this->assertInstanceOf(GroupInterface::class, $group);
    }

    /**
     * @test
     * @ticket CORE-707
     */
    public function testItAddNumbersToStudentUserNames()
    {
        $this->logInUser('super_user');
        $importer = NycDoeTestImporterSetup::getImporter();
        $importer->exchangeArray([
            'file'         => __DIR__ . '/_files/test_sheet.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'school',
            'email'        => 'test@example.com',
        ]);

        $importer->perform();

        // Principal Added?
        $principal = $this->userService->fetchUserByEmail('sandoval@gmail.com');
        $this->assertInstanceOf(Adult::class, $principal);

        // Teacher Added?
        $teacher = $this->userService->fetchUserByEmail('solomon@gmail.com');
        $this->assertInstanceOf(Adult::class, $teacher);

        // Student Added?
        $student = $this->userService->fetchUserByExternalId('01X100-123');
        $this->assertInstanceOf(Child::class, $student);

        $this->assertRegExp(
            '/^[a-z]+-[a-z]+\d{3}$/',
            $student->getUserName(),
            'Importer did not make user names for children' . $student->getUserName()
        );
    }
}
