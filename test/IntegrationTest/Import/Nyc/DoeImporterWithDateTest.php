<?php

namespace IntegrationTest\Import\Nyc;

use Application\Utils\Date\DateTimeFactory;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\DbUnitConnectionTrait;
use IntegrationTest\LoginUserTrait;
use IntegrationTest\TestHelper;
use Lcobucci\JWT\Parser;
use PHPUnit\DbUnit\TestCase as TestCase;
use Security\Service\SecurityServiceInterface;

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
 * @group Security
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class DoeImporterWithDateTest extends TestCase
{
    use DbUnitConnectionTrait;
    use LoginUserTrait;

    /**
     * @var SecurityServiceInterface
     */
    protected $securityService;

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
        $this->securityService = TestHelper::getDbServiceManager()->get(SecurityServiceInterface::class);
    }

    /**
     * @test
     * @ticket CORE-2713
     */
    public function testItShouldImportDataWithDateTime()
    {
        $codeStart = DateTimeFactory::factory('tomorrow');
        $this->logInUser('super_user');
        $importer = NycDoeTestImporterSetup::getImporter();
        $importer->exchangeArray([
            'file'         => __DIR__ . '/_files/test_sheet.xlsx',
            'teacher_code' => 'Apple0007',
            'student_code' => 'pear0007',
            'school'       => 'school',
            'email'        => 'test@example.com',
            'code_start'   => $codeStart
        ]);

        $importer->perform();

        $user = $this->securityService->fetchUserByEmail('sandoval@gmail.com');

        $compareToken = (new Parser())->parse($user->getCode());
        $this->assertEquals(
            $codeStart->getTimestamp(),
            $compareToken->getClaim('nbf'),
            'Code does not have correct nbf claim'
        );
    }
}
