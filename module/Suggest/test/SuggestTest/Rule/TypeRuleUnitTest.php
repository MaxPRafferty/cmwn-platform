<?php

namespace SuggestTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Rule\TypeRule;
use Suggest\SuggestionCollection;
use User\Adult;
use User\Child;

/**
 * Class TypeRuleUnitTest
 * @package SuggestTest\Rule
 */
class TypeRuleUnitTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldRemoveSuggestionsOfDifferentType()
    {
        $currentUser = new Child(['user_id' => 'current_user']);
        $notFriends1 = new Child(['user_id' => 'not_friends']);
        $adultUser   = new Adult(['user_id' => 'adult_user']);
        $collection  = new SuggestionCollection();
        $collection->append($adultUser);
        $collection->append($notFriends1);

        $rule = new TypeRule();

        $rule->apply($collection, $currentUser);

        $this->assertEquals(
            ['not_friends' => $notFriends1],
            $collection->getArrayCopy(),
            'Adult was not removed from the suggestion collection'
        );
    }
}
