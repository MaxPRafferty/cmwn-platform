<?php

namespace IntegrationTest\Service;

use Group\Service\GroupServiceInterface;
use Group\Group;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\LoginUserTrait;
use IntegrationTest\TestHelper;
use IntegrationTest\AbstractDbTestCase as TestCase;
use Org\Organization;
use Org\Service\OrganizationServiceInterface;

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
    use LoginUserTrait;

    /**
     * @var GroupServiceInterface
     */
    protected $groupService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../DataSets/group.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserGroupService()
    {
        $this->groupService = TestHelper::getDbServiceManager()->get(GroupServiceInterface::class);
    }

    /**
     * @before
     */
    public function setUpLogin()
    {
        $this->logInUser('super_user');
    }

    /**
     * @return array
     */
    public function testItShouldReBalanceTheNetworkCorrectlyInTheDatabase()
    {
        $district = new Organization([
            'org_id' => 'network_district',
            'title'  => 'Test network district',
            'type'   => 'network_district',
        ]);

        /** @var OrganizationServiceInterface $orgService */
        $orgService = TestHelper::getDbServiceManager()->get(OrganizationServiceInterface::class);
        $orgService->createOrganization($district);

        $schoolOne = new Group([
            'type'            => 'school',
            'title'           => 'School 1',
            'organization_id' => $district->getOrgId(),
        ]);
        $schoolOne->setOrganizationId($district);

        $schoolTwo = new Group([
            'type'            => 'school',
            'title'           => 'School 2',
            'organization_id' => $district->getOrgId(),
        ]);
        $schoolTwo->setOrganizationId($district);

        $mathForSchoolOne = new Group([
            'type'            => 'class',
            'title'           => 'Math for school 1',
            'organization_id' => $district->getOrgId(),
        ]);
        $mathForSchoolOne->setOrganizationId($district);

        $mathForSchoolTwo = new Group([
            'type'            => 'class',
            'title'           => 'Math for school 2',
            'organization_id' => $district->getOrgId(),
        ]);
        $mathForSchoolTwo->setOrganizationId($district);

        $lunchForSchoolOne = new Group([
            'type'            => 'class',
            'title'           => 'Lunch for school 1',
            'organization_id' => $district->getOrgId(),
        ]);
        $lunchForSchoolOne->setOrganizationId($district);

        $lunchForSchoolTwo = new Group([
            'type'            => 'class',
            'title'           => 'Lunch for school 2',
            'organization_id' => $district->getOrgId(),
        ]);
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
