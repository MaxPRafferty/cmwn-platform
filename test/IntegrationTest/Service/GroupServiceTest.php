<?php

namespace IntegrationTest\Service;

use Group\Service\GroupServiceInterface;
use Group\Group;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;

/**
 * Exception GroupServiceTest
 *
 * @group Group
 * @group IntegrationTest
 * @group GroupService
 * @group DB
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GroupServiceTest extends TestCase
{
    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->groupService = TestHelper::getServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @return array
     */
    public function testItShouldReBalanceTheNetworkCorrectlyInTheDatabase()
    {
        $district = new Organization([
            'org_id' => 'network_district',
            'title'  => 'Test network district',
            'type'   => 'district',
        ]);

        /** @var OrganizationServiceInterface $orgService */
        $orgService = TestHelper::getServiceManager()->get(OrganizationServiceInterface::class);
        $orgService->createOrganization($district);

        $schoolOne = new Group(['type' => 'school', 'title' => 'School 1', 'organization_id' => '']);
        $schoolOne->setOrganizationId($district);

        $schoolTwo = new Group(['type' => 'school', 'title' => 'School 2']);
        $schoolTwo->setOrganizationId($district);

        $mathForSchoolOne = new Group(['type' => 'class', 'title' => 'Math for school 1']);
        $mathForSchoolOne->setOrganizationId($district);

        $mathForSchoolTwo = new Group(['type' => 'class', 'title' => 'Math for school 2']);
        $mathForSchoolTwo->setOrganizationId($district);

        $lunchForSchoolOne = new Group(['type' => 'class', 'title' => 'Lunch for school 1']);
        $lunchForSchoolOne->setOrganizationId($district);

        $lunchForSchoolTwo = new Group(['type' => 'class', 'title' => 'Lunch for school 2']);
        $lunchForSchoolTwo->setOrganizationId($district);

        $this->groupService->createGroup($schoolOne);
        $this->groupService->createGroup($schoolTwo);
        $this->groupService->createGroup($mathForSchoolOne);
        $this->groupService->createGroup($mathForSchoolTwo);
        $this->groupService->createGroup($lunchForSchoolOne);
        $this->groupService->createGroup($lunchForSchoolTwo);

        $this->groupService->addChildToGroup($schoolOne, $mathForSchoolOne);
        $this->groupService->addChildToGroup($schoolTwo, $mathForSchoolTwo);

        $this->groupService->addChildToGroup($mathForSchoolOne, $lunchForSchoolOne);
        $this->groupService->addChildToGroup($mathForSchoolTwo, $lunchForSchoolTwo);

        $updatedSchoolOne = $this->groupService->fetchGroup($schoolOne->getGroupId());
        $updatedMathOne   = $this->groupService->fetchGroup($mathForSchoolOne->getGroupId());
        $updatedLunchOne  = $this->groupService->fetchGroup($lunchForSchoolOne->getGroupId());

        $this->assertEquals(
            '1',
            $updatedSchoolOne->getHead(),
            'Head for school 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '6',
            $updatedSchoolOne->getTail(),
            'Tail for school 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '2',
            $updatedMathOne->getHead(),
            'Head for math 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '5',
            $updatedMathOne->getTail(),
            'Tail for math 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '3',
            $updatedLunchOne->getHead(),
            'Head for Lunch 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '4',
            $updatedLunchOne->getTail(),
            'Tail for Lunch 1 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            $schoolOne->getNetworkId(),
            $updatedMathOne->getNetworkId(),
            'Math one was not attached to the same network as school 1'
        );

        $this->assertEquals(
            $schoolOne->getNetworkId(),
            $updatedLunchOne->getNetworkId(),
            'Lunch one was not attached to the same network as lunch 1'
        );

        $updatedSchoolTwo = $this->groupService->fetchGroup($schoolTwo->getGroupId());
        $updatedMathTwo   = $this->groupService->fetchGroup($mathForSchoolTwo->getGroupId());
        $updatedLunchTwo  = $this->groupService->fetchGroup($lunchForSchoolTwo->getGroupId());

        $this->assertEquals(
            '1',
            $updatedSchoolTwo->getHead(),
            'Head for school 2 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '6',
            $updatedSchoolTwo->getTail(),
            'Tail for school 2 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '2',
            $updatedMathTwo->getHead(),
            'Head for math 2 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '5',
            $updatedMathTwo->getTail(),
            'Tail for math 2 is incorrect after Attaching Class'
        );
        $this->assertEquals(
            '3',
            $updatedLunchTwo->getHead(),
            'Head for lunch 2 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            '4',
            $updatedLunchTwo->getTail(),
            'Tail for lunch 2 is incorrect after Attaching Class'
        );

        $this->assertEquals(
            $schoolTwo->getNetworkId(),
            $updatedMathTwo->getNetworkId(),
            'Math two was not attached to the same network as school 2'
        );

        $this->assertEquals(
            $schoolTwo->getNetworkId(),
            $updatedLunchTwo->getNetworkId(),
            'Lunch two was not attached to the same network as school 2'
        );
    }
}
