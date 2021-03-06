<?php

namespace SaTest\V1\Rest;

use PHPUnit\Framework\TestCase as TestCase;
use Sa\V1\Rest\SuperAdminSettings\SuperAdminSettingsEntity;
use ZF\Hal\Link\LinkCollection;

/**
 * Class SuperAdminSettingsEntityTest
 * @package SaTest\V1\Rest
 */
class SuperAdminSettingsEntityTest extends TestCase
{
    /**
     * @var SuperAdminSettingsEntity
     */
    protected $entity;

    /**
     * @before
     */
    protected function setUpEntity()
    {
        $this->entity = new SuperAdminSettingsEntity([
            'cmwn-roles' => [
                'roles' => [
                    'foo' => ['db-role' => false],
                    'admin.adult' => ['db-role' => true],
                    'asst_principal.adult' => ['db-role' => true],
                    'principal.adult' => ['db-role' => true],
                    'student.adult' => ['db-role' => true],
                    'teacher.adult' => ['db-role' => true],
                ],
            ],
        ]);
    }

    /**
     * @test
     */
    public function testItShouldCorrectlyAddLinks()
    {
        /**@var LinkCollection $links*/
        $links = $this->entity->getLinks();
        $this->assertInstanceOf(LinkCollection::class, $links);
        $this->assertTrue($links->has('user'));
        $this->assertTrue($links->has('games_deleted'));
        $this->assertTrue($links->has('game-data'));
        $this->assertTrue($links->has('group'));
        $this->assertTrue($links->has('org'));
        $this->assertTrue($links->has('flip'));

        $this->assertEquals($links->count(), 6);
    }

    /**
     * @test
     */
    public function testItShouldCorrectlySetRoles()
    {
        $roles = $this->entity->getRoles();
        $this->assertEquals($roles, ['group' => ['admin', 'asst_principal', 'principal', 'student', 'teacher']]);
    }
}
