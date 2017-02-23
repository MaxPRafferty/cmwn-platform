<?php

namespace FlipTest\Rule\Action;

use Flip\Rule\Action\EarnFlip;
use Flip\Service\FlipUserServiceInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as TestCase;
use Rule\Exception\InvalidProviderType;
use Rule\Item\BasicRuleItem;
use Rule\Provider\BasicValueProvider;
use User\Child;

/**
 * Test EarnFlipTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class EarnFlipTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var \Mockery\MockInterface|FlipUserServiceInterface
     */
    protected $userFlipService;

    /**
     * @before
     */
    public function setUpFlipUserService()
    {
        $this->userFlipService = \Mockery::mock(FlipUserServiceInterface::class);
    }

    /**
     * @test
     */
    public function testItShouldAttachTheFlipToTheUser()
    {
        $action = new EarnFlip(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );
        $user   = new Child();

        $ruleItem = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->userFlipService->shouldReceive('attachFlipToUser')
            ->with($user, 'foo-bar-flip')
            ->once();

        $this->assertEmpty(
            $action($ruleItem),
            EarnFlip::class . ' has invalid return for __invoke'
        );
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenUserIsNotAUser()
    {
        $this->expectException(InvalidProviderType::class);
        $action   = new EarnFlip(
            $this->userFlipService,
            'foo-bar-flip',
            'user'
        );
        $user     = new \stdClass();
        $ruleItem = new BasicRuleItem(new BasicValueProvider('user', $user));

        $this->userFlipService->shouldReceive('attachFlipToUser')->never();

        $action($ruleItem);
    }
}
