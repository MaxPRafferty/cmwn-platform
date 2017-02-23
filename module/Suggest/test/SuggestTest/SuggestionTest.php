<?php

namespace SuggestTest;

use PHPUnit\Framework\TestCase as TestCase;
use Suggest\Suggestion;
use User\Child;

/**
 * Test SuggestionTest
 *
 * @group Suggestion
 * @group Friend
 * @group User
 * @group Child
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SuggestionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldBeAbleToExtractToArrayCorrectly()
    {
        $user          = new Child(['user_id' => 'manchuck', 'username' => 'manchuck']);
        $expectedArray = array_merge(
            $user->getArrayCopy(),
            ['friend_status' => 'NOT_FRIENDS']
        );

        $suggestion = new Suggestion($expectedArray);
        unset($expectedArray['user_id']);
        $expectedArray['suggest_id'] = $user->getUserId();

        $this->assertEquals(
            $expectedArray,
            $suggestion->getArrayCopy(),
            'Suggestion did not hydrate or extract correctly'
        );
    }
}
