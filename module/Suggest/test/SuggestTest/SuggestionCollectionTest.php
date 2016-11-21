<?php

namespace SuggestTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\InvalidSuggestionException;
use Suggest\SuggestionCollection;
use User\Child;

/**
 * Test SuggestionCollectionTest
 *
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class SuggestionCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldOnlyAllowUsersToBeAdded()
    {
        $this->setExpectedException(InvalidSuggestionException::class);
        $collection = new SuggestionCollection();
        $collection->append(new \stdClass());
    }

    /**
     * @test
     */
    public function testItShouldAddUsers()
    {
        $userOne = new Child(['user_id' => 'foo']);
        $userTwo = new Child(['user_id' => 'bar']);

        $collection = new SuggestionCollection();
        $collection->append($userOne);
        $collection->append($userTwo);

        $this->assertEquals(
            ['foo', 'bar'],
            array_keys($collection->getArrayCopy())
        );
    }

    /**
     * @test
     */
    public function testItShouldBeIteratedCorrectly()
    {
        $userOne = new Child(['user_id' => 'foo']);
        $userTwo = new Child(['user_id' => 'bar']);

        $collection = new SuggestionCollection();
        $collection->append($userOne);
        $collection->append($userTwo);

        $actual = [];

        iterator_apply(
            $collection->getIterator(),
            function (\Iterator $iterator) use (&$actual) {
                array_push($actual, $iterator->current()->getUserId());
                $iterator->next();
                return true;
            },
            [$collection->getIterator()]
        );


        $this->assertEquals(
            ['foo', 'bar'],
            $actual
        );
    }
}
