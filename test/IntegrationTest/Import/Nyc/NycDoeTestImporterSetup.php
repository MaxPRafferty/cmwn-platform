<?php

namespace IntegrationTest\Import\Nyc;

use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\DoeImporter;
use Import\Importer\Nyc\Parser\DoeParser;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use IntegrationTest\TestHelper;
use Security\Authorization\Rbac;
use Security\Authorization\RbacAwareInterface;
use Security\Service\SecurityServiceInterface;
use User\Service\UserServiceInterface;

/**
 * Class NycDoeTestImporterSetup
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class NycDoeTestImporterSetup
{
    /**
     * @var UserServiceInterface
     */
    protected static $userService;

    /**
     * @var GroupServiceInterface
     */
    protected static $groupService;

    /**
     * @var UserGroupServiceInterface
     */
    protected static $userGroupService;

    /**
     * @var SecurityServiceInterface
     */
    protected static $securityService;

    /**
     * @var Rbac
     */
    protected static $rbac;

    /**
     * @return UserServiceInterface
     */
    public static function getUserService()
    {
        if (static::$userService === null) {
            static::$userService = TestHelper::getServiceManager()->get(UserServiceInterface::class);
        }

        return static::$userService;
    }

    /**
     * @return GroupServiceInterface
     */
    public static function getGroupService()
    {
        if (static::$groupService === null) {
            static::$groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
        }

        return static::$groupService;
    }

    /**
     * @return UserGroupServiceInterface
     */
    public static function getUserGroupService()
    {
        if (static::$userGroupService === null) {
            static::$userGroupService = TestHelper::getServiceManager()->get(UserGroupServiceInterface::class);
        }

        return static::$userGroupService;
    }

    /**
     * @return SecurityServiceInterface
     */
    public static function getSecurityService()
    {
        if (static::$securityService === null) {
            static::$securityService = TestHelper::getServiceManager()->get(SecurityServiceInterface::class);
        }

        return static::$securityService;
    }

    /**
     * @return Rbac
     */
    public static function getRbac()
    {
        if (static::$rbac === null) {
            static::$rbac = TestHelper::getServiceManager()->get(Rbac::class);
        }

        return static::$rbac;
    }

    /**
     * @return StudentRegistry
     */
    public static function getStudentRegistry()
    {
        return new StudentRegistry(static::getUserService());
    }

    /**
     * @return TeacherRegistry
     */
    public static function getTeacherRegistry()
    {
        return new TeacherRegistry(static::getUserService());
    }

    /**
     * @return ClassRoomRegistry
     */
    public static function getClassroomRegistry()
    {
        return new ClassRoomRegistry(static::getGroupService());
    }

    /**
     * @return DoeParser
     */
    public static function getParser()
    {
        DoeParser::clear();
        $parser = new DoeParser(
            static::getClassroomRegistry(),
            static::getTeacherRegistry(),
            static::getStudentRegistry(),
            static::getUserGroupService(),
            static::getGroupService(),
            static::getSecurityService()
        );

        $parser->setRbac(static::getRbac());
        return $parser;
    }

    /**
     * @return DoeImporter
     */
    public static function getImporter()
    {
        return new DoeImporter(
            static::getParser(),
            static::getGroupService()
        );
    }
}
