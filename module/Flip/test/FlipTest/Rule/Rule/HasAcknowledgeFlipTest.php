<?php

namespace FlipTest\Rule\Rule;

use Application\Exception\NotFoundException;
use Flip\EarnedFlip;
use Flip\Rule\Provider\AcknowledgeFlip;
use Flip\Rule\Rule\HasAcknowledgeFlip;
use Flip\Service\FlipUserServiceInterface;
use PHPUnit\Framework\TestCase as TestCase;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use Security\Rule\Provider\ActiveUserProvider;
use User\Child;

/**
 * Test HasAcknowledgeFlipTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HasAcknowledgeFlipTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface|FlipUserServiceInterface
     */
    protected $flipUserService;

    /**
     * @before
     */
    public function setUpFlipUserServiceInterface()
    {
        $this->flipUserService = \Mockery::mock(FlipUserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldBeSatisfiedWhenUserHasAFlipToAcknowledge()
    {
        $user = new Child();
        $flip = new EarnedFlip();
        $rule = new HasAcknowledgeFlip($this->flipUserService);
        $item = new BasicRuleItem(
            new BasicValueProvider(ActiveUserProvider::PROVIDER_NAME, $user)
        );

        $flip->setAcknowledgeId('foobar');

        $this->flipUserService->shouldReceive('fetchLatestAcknowledgeFlip')
            ->with($user)
            ->andReturn($flip)
            ->once();

        $this->assertTrue(
            $rule->isSatisfiedBy($item),
            HasAcknowledgeFlip::class . ' is not happy.  Why is it not happy?'
        );

        $this->assertSame(
            $item->getParam(AcknowledgeFlip::PROVIDER_NAME),
            $flip,
            HasAcknowledgeFlip::class . ' did not append the flip to the rule'
        );

        $this->assertSame(
            1,
            $rule->timesSatisfied(),
            HasAcknowledgeFlip::class . ' did not set the times satisfied correctly'
        );
    }

    /**
     * @test
     */
    public function testItShouldNotBeSatisfiedWhenThereIsNoFlip()
    {
        $user = new Child();
        $rule = new HasAcknowledgeFlip($this->flipUserService);
        $item = new BasicRuleItem(
            new BasicValueProvider(ActiveUserProvider::PROVIDER_NAME, $user)
        );

        $this->flipUserService->shouldReceive('fetchLatestAcknowledgeFlip')
            ->with($user)
            ->andThrow(new NotFoundException())
            ->once();

        $this->assertFalse(
            $rule->isSatisfiedBy($item),
            HasAcknowledgeFlip::class . ' is happy.'
        );

        $this->assertSame(
            0,
            $rule->timesSatisfied(),
            HasAcknowledgeFlip::class . ' is saying it is too happy'
        );
    }
}
