<?php

namespace IntegrationTest\Service;

use Application\Exception\NotFoundException;
use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use Flip\Flip;
use Flip\FlipInterface;
use Flip\Rule\Provider\AcknowledgeFlip;
use Flip\Service\FlipUserService;
use IntegrationTest\AbstractDbTestCase as TestCase;
use IntegrationTest\DataSets\ArrayDataSet;
use IntegrationTest\TestHelper;
use User\Child;
use Zend\Paginator\Paginator;

/**
 * Test FlipUserServiceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipUserServiceTest extends TestCase
{
    /**
     * @var FlipUserService
     */
    protected $flipUserService;

    /**
     * @return ArrayDataSet
     */
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/../DataSets/flip-user.dataset.php');
    }

    /**
     * @before
     */
    public function setUpUserFlipService()
    {
        $this->flipUserService = TestHelper::getServiceManager()->get(FlipUserService::class);
        $this->flipUserService->getEventManager()->clearListeners('attach.flip.post');
    }

    /**
     * @test
     */
    public function testItShouldReturnEarlistFlipToBeAcknowledged()
    {
        $user       = new Child(['user_id' => 'needs_acknowledge']);
        $earnedFlip = $this->flipUserService->fetchLatestAcknowledgeFlip($user);

        $this->assertInstanceOf(
            EarnedFlipInterface::class,
            $earnedFlip,
            FlipUserService::class . ' did not return correct flip with no prototype'
        );

        $this->assertSame(
            [
                'flip_id'        => 'manchuck-farmville',
                'title'          => 'Be an Awesome Farmer',
                'description'    => 'Become an Awesome Farmer!',
                'earned'         => '2016-04-16T11:58:15+0000',
                'acknowledge_id' => 'baz-bat',
                'earned_by'      => 'needs_acknowledge',
            ],
            $earnedFlip->getArrayCopy(),
            FlipUserService::class . ' did not hydrate the earned flip correctly'
        );
    }

    /**
     * @test
     * @dataProvider noFlipsProvider
     *
     * @param $userId
     */
    public function testItShouldThrowExceptionWhenThereAreNoFlipsToAcknowledge($userId)
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('No flips to acknowledge');
        $user = new Child(['user_id' => $userId]);

        $this->flipUserService->fetchLatestAcknowledgeFlip($user);
    }

    /**
     * @test
     * @dataProvider acknowledgeFlipProvider
     *
     * @param $userId
     * @param $ackId
     */
    public function testItShouldAcknowledgeFlip($userId, $ackId)
    {
        $flip = new EarnedFlip(['acknowledge_id' => (string)$ackId]);
        $this->assertTrue(
            $this->flipUserService->acknowledgeFlip($flip),
            FlipUserService::class . ' did not return true for acknowledge flip'
        );

        $this->assertEquals(
            0,
            $this->getConnection()->getRowCount(
                'user_flips',
                sprintf('user_id = "%s" AND acknowledge_id = "%s"', $userId, $ackId)
            ),
            FlipUserService::class . ' did not set the acknowledge Id correctly'
        );
    }

//    /**
//     * @test
//     */
//    public function testItShouldAttachFlipToUserWithJustIds()
//    {
//        $this->flipUserService->attachFlipToUser('no_flips', 'manchuck-farmville');
//
//        $this->assertEquals(
//            1,
//            $this->getConnection()->getRowCount(
//                'user_flips',
//                sprintf('user_id = "%s" AND flip_id = "%s"', 'no_flips', 'manchuck-farmville')
//            ),
//            FlipUserService::class . ' did not set the acknowledge Id correctly'
//        );
//    }

    /**
     * @test
     */
    public function testItShouldAttachFlipToUserWithINstances()
    {
        $user = new Child(['user_id' => 'no_flips']);
        $flip = new Flip(['flip_id' => 'manchuck-farmville']);
        $this->flipUserService->attachFlipToUser($user, $flip);

        $this->assertEquals(
            1,
            $this->getConnection()->getRowCount(
                'user_flips',
                sprintf('user_id = "%s" AND flip_id = "%s"', 'no_flips', 'manchuck-farmville')
            ),
            FlipUserService::class . ' did not set the acknowledge Id correctly'
        );
    }

    /**
     * @test
     * @dataProvider earnedFlipsProvider
     *
     * @param $userId
     * @param array $expectedFlipsIds
     */
    public function testItShouldReturnEarnedFlipsForUser($userId, array $expectedFlipsIds)
    {
        $flips     = new Paginator($this->flipUserService->fetchEarnedFlipsForUser($userId));
        $actualIds = [];

        foreach ($flips as $earnedFlip) {
            /** @var FlipInterface $earnedFlip */
            $this->assertInstanceOf(
                FlipInterface::class,
                $earnedFlip,
                FlipUserService::class . ' did not return a flip interface'
            );

            array_push($actualIds, $earnedFlip->getFlipId());
        }

        $this->assertEquals(
            $expectedFlipsIds,
            $actualIds,
            FlipUserService::class . ' Returned the wrong flips'
        );
    }

    /**
     * @test
     * @dataProvider userFlipsProvider
     *
     * @param $userId
     * @param array $expectedFlipsIds
     */
    public function testItShouldFetchAllFlipsForUser($userId, $flipId, array $expectedFlipsIds)
    {
        $user      = new Child(['user_id' => $userId]);
        $flips     = new Paginator($this->flipUserService->fetchFlipsForUser($user, $flipId));
        $actualIds = [];

        foreach ($flips as $earnedFlip) {
            /** @var FlipInterface $earnedFlip */
            $this->assertInstanceOf(
                FlipInterface::class,
                $earnedFlip,
                FlipUserService::class . ' did not return a flip interface'
            );

            array_push($actualIds, $earnedFlip->getFlipId());
        }

        $this->assertEquals(
            $expectedFlipsIds,
            $actualIds,
            FlipUserService::class . ' Returned the wrong flips'
        );
    }

    /**
     * @return array
     */
    public function userFlipsProvider()
    {
        return [
            'Multiple Earned' => [
                'user_id'           => 'needs_acknowledge',
                'flip_id'           => 'manchuck-farmville',
                'expected_flip_ids' => ['manchuck-farmville', 'manchuck-farmville'],
            ],

            'One Earned' => [
                'user_id'           => 'already_acknowledge',
                'flip_id'           => 'manchuck-farmville',
                'expected_flip_ids' => ['manchuck-farmville'],
            ],

            'No Flips' => [
                'user_id'           => 'no-flips',
                'flip_id'           => 'manchuck-farmville',
                'expected_flip_ids' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function earnedFlipsProvider()
    {
        return [
            'Multiple Earned' => [
                'user_id'           => 'needs_acknowledge',
                'expected_flip_ids' => ['manchuck-farmville'],
            ],

            'One Earned' => [
                'user_id'           => 'already_acknowledge',
                'expected_flip_ids' => ['manchuck-farmville'],
            ],

            'No Flips' => [
                'user_id'           => 'no-flips',
                'expected_flip_ids' => [],
            ],
        ];
    }

    /**
     * @return array
     */
    public function acknowledgeFlipProvider()
    {
        return [
            'Needs to Acknowledge' => ['needs_acknowledge', 'baz-bat'],
            'Already Acknowledged' => ['already_acknowledged', null],
        ];
    }

    /**
     * @return array
     */
    public function noFlipsProvider()
    {
        return [
            'Has No Flips'           => ['no_flips'],
            'All Flips Acknowledged' => ['already_acknowledged'],
        ];
    }
}
