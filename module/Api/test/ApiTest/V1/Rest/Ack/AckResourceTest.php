<?php

namespace ApiTest\V1\Rest\Ack;

use Api\V1\Rest\EarnedFlip\AckResource;
use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use Flip\Service\FlipUserServiceInterface;
use PHPUnit\Framework\TestCase;
use ZF\ApiProblem\ApiProblem;

/**
 * Test AckResourceTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AckResourceTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCallAcknowledgeFlipOnService()
    {
        /** @var \Mockery\MockInterface|FlipUserServiceInterface $flipService */
        $flipService = \Mockery::mock(FlipUserServiceInterface::class);
        $resource = new AckResource($flipService);

        $flipService->shouldReceive('acknowledgeFlip')
            ->once()
            ->andReturnUsing(function (EarnedFlipInterface $actual) {
                $this->assertEquals(
                    new EarnedFlip(['acknowledge_id' => 'foo-bar']),
                    $actual,
                    AckResource::class . ' is not calling acknowledgeFlip correctly'
                );

                return true;
            });

        $this->assertEquals(
            new ApiProblem(204, 'Acknowledged'),
            $resource->update('foo-bar', []),
            AckResource::class . ' is not returning the expected ApiProblem'
        );
    }
}
