<?php

use Phinx\Seed\AbstractSeed;

/**
 * Class TestSeed
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD)
 */
class TestSeed extends AbstractSeed
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $testDataConfig = require __DIR__ . '/../../config/autoload/test-data.global.php';

        $testData = $testDataConfig['test-data'];

        foreach ($testData as $tableName => $tableData) {
            $table = $this->table($tableName);

            if (in_array($tableName, ['organizations', 'groups', 'users', 'images'])) {
                array_walk($tableData, function (&$data){
                    $date = new \DateTime();
                    $data['created'] = $date->format("Y-m-d H:i:s");
                    $data['updated'] = $date->format("Y-m-d H:i:s");
                });
            }

            if ($tableName === 'user_flips') {
                array_walk($tableData, function (&$data){
                    $date = new \DateTime();
                    $data['earned'] = $date->format("Y-m-d H:i:s");
                });
            }
            try {
                $this->getOutput()->writeln(sprintf('Adding test data to %s table', $tableName));
                $table->insert($tableData)
                    ->save();
            } catch (\PDOException $pdo) {
                if ($pdo->getCode() != 23000) {
                    $this->getOutput()->writeLn(
                        sprintf(
                            'Got Exception When trying to insert data to %s table',
                            $tableName
                        )
                    );
                }
            }
        }
    }
}
