<?php

namespace SuggestTest\Rule;

use PHPUnit\Framework\TestCase;
use Suggest\Rule\MeRule;
use Suggest\SuggestionCollection;
use User\Child;

/**
 * Class MeRuleTest
 *
 * @group User
 * @group Suggest
 * @group Rule
 */
class MeRuleTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldRemoveSuggestionIfMeUser()
    {
        $currentUser = new Child(['user_id' => 'current_user']);
        $notFriends1 = new Child(['user_id' => 'not_friends']);
        $collection  = new SuggestionCollection();
        $collection->append($currentUser);
        $collection->append($notFriends1);

        $rule = new MeRule();

        $rule->apply($collection, $currentUser);

        $this->assertEquals(
            ['not_friends' => $notFriends1],
            $collection->getArrayCopy(),
            '"Me" was not removed from the suggestion collection'
        );
    }
}
