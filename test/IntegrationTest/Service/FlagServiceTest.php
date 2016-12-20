<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use Flag\Flag;
use Flag\FlagHydrator;
use Flag\FlagInterface;
use Flag\Service\FlagService;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\TestHelper;
use User\Child;
use User\Service\UserService;
use User\UserInterface;
use IntegrationTest\DataSets\ArrayDataSet;

/**
 * Class FlagServiceTest
 * @group Flag
 * @group FlagService
 * @group IntegrationTest
 * @group Db
 * @package IntegrationTest\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlagServiceTest extends TestCase
{
    /**
     * @var FlagHydrator
     */
    protected $flagHydrator;

    /**
     * @var FlagService
     */
    protected $flagService;

    /**
     * @var Flag
     */
    protected $flag;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return new ArrayDataSet(include __DIR__ . '/../DataSets/flag.dataset.php');
    }

    /**
     * @before
     */
    public function setUpFlag()
    {
        $flagData = [
            'flagger' => new Child(['user_id' => 'english_student']),
            'flaggee' => new Child(['user_id' => 'english_student']),
            'url'     => '/foo',
            'reason'  => 'bar'
        ];
        $this->flag = new Flag($flagData);
    }

    /**
     * @before
     */
    public function setUpFlagHydrator()
    {
        $userService = TestHelper::getDbServiceManager()->get(UserService::class);

        $this->flagHydrator = new FlagHydrator($userService);
    }

    /**
     * @before
     */
    public function setUpFlagService()
    {
        $this->flagService = TestHelper::getDbServiceManager()->get(FlagService::class);
    }

    /**
     * @test
     */
    public function testItShouldFetchAllFlags()
    {
        $flags = $this->flagService->fetchAll();
        $this->assertEquals(1, $flags->count());
        $flag = $flags->getItems(0, 1)[0];
        $this->assertInstanceOf(FlagInterface::class, $flag);
        $this->assertInstanceOf(UserInterface::class, $flag->getFlagger());
        $this->assertInstanceOf(UserInterface::class, $flag->getFlaggee());
        $this->assertEquals('flagged-image', $flag->getFlagId());
        $this->assertEquals('http://read.bi/2clh0wi', $flag->getUrl());
        $this->assertEquals('Offensive ;)', $flag->getReason());
        $this->assertEquals('english_student', $flag->getFlaggee()->getUserId());
        $this->assertEquals('math_student', $flag->getFlagger()->getUserId());
    }

    /**
     * @test
     */
    public function testItShouldFetchFlagById()
    {
        $flag = $this->flagService->fetchFlag('flagged-image');
        $this->assertInstanceOf(FlagInterface::class, $flag);
        $this->assertInstanceOf(UserInterface::class, $flag->getFlagger());
        $this->assertInstanceOf(UserInterface::class, $flag->getFlaggee());
        $this->assertEquals('flagged-image', $flag->getFlagId());
        $this->assertEquals('http://read.bi/2clh0wi', $flag->getUrl());
        $this->assertEquals('Offensive ;)', $flag->getReason());
        $this->assertEquals('english_student', $flag->getFlaggee()->getUserId());
        $this->assertEquals('math_student', $flag->getFlagger()->getUserId());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenFlagNotFound()
    {
        $this->setExpectedException(NotFoundException::class);
        $this->flagService->fetchFlag('foo');
    }

    /**
     * @test
     */
    public function testItShouldSaveFlag()
    {
        $this->flagService->saveFlag($this->flag);

        $flag = $this->flagService->fetchFlag($this->flag->getFlagId());
        $this->assertEquals($flag->getUrl(), $this->flag->getUrl());
        $this->assertEquals($flag->getReason(), $this->flag->getReason());
        $this->assertInstanceOf(UserInterface::class, $flag->getFlagger());
        $this->assertInstanceOf(UserInterface::class, $flag->getFlagger());
    }

    /**
     * @test
     */
    public function testItShouldUpdateFlag()
    {
        $this->flagService->saveFlag($this->flag);

        $flagData = [
            'flag_id' => $this->flag->getFlagId(),
            'flagger' => new Child(['user_id' => 'english_student']),
            'flaggee' => new Child(['user_id' => 'english_student']),
            'url'     => '/baz',
            'reason'  => 'bat'
        ];

        $this->flagService->updateFlag(new Flag($flagData));

        $flag = $this->flagService->fetchFlag($this->flag->getFlagId());

        $this->assertEquals('/baz', $flag->getUrl());
        $this->assertEquals('bat', $flag->getReason());
    }

    /**
     * @test
     */
    public function testItShouldDeleteFlag()
    {
        $flag = $this->flagService->fetchFlag('flagged-image');
        $this->flagService->deleteFlag($flag);
        $flags = $this->flagService->fetchAll();
        $this->assertEquals(0, $flags->count());
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenAttemptingToDeleteNonExistentFlag()
    {
        $this->setExpectedException(NotFoundException::class);
        $this->flagService->deleteFlag($this->flag);
    }
}
