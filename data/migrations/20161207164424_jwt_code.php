<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Phinx\Migration\AbstractMigration;

class JwtCode extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('users');
        if ($table->hasColumn('code')) {
            $table->changeColumn('code', 'text')
                ->update();
        }

        // Get all users that have codes
        $usersWithCodes = $this->fetchAll('SELECT * FROM users WHERE code IS NOT NULL');
        foreach ($usersWithCodes as $userData) {
            if (empty($userData['code'])) {
                continue;
            }

            $expires = new \DateTime($userData['code_expires']);
            $expires->setTime(23, 59, 59);

            $updated   = new \DateTime($userData['updated']);
            $jwtConfig = new \Lcobucci\JWT\Configuration();
            $token     = $jwtConfig->createBuilder()
                ->canOnlyBeUsedBy($userData['user_id'])
                ->issuedAt($updated->getTimestamp())
                ->canOnlyBeUsedAfter($updated->getTimestamp())
                ->expiresAt($expires->getTimestamp())
                ->identifiedBy($userData['code'])
                ->getToken();

            $this->getOutput()->writeln(sprintf('Changing code [%s] for: %s', $userData['code'], $userData['user_id']));
            // Update to JWT Token
            $this->execute(
                'UPDATE users ' .
                'SET code = "' . $token->__toString() . '"' .
                'WHERE user_id = "' . $userData['user_id'] . '"'
            );
        }

        // Remove other columns
        if ($table->hasColumn('code_starts')) {
            $table->removeColumn('code_starts');
        }

        if ($table->hasColumn('code_expires')) {
            $table->removeColumn('code_expires');
        }

        $table->update();
    }

    public function down()
    {

    }
}
