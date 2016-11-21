<?php

namespace SuggestTest\Rule;

use \PHPUnit_Framework_TestCase as TestCase;
use Suggest\Rule\TypeRule;
use Suggest\SuggestionCollection;
use User\Adult;
use User\Child;
use User\UserInterface;

/**
 * Class TypeRuleUnitTest
 * @package SuggestTest\Rule
 */
class TypeRuleUnitTest extends TestCase
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SuggestionCollection
     */
    protected $container;

    /**
     * @var TypeRule
     */
    protected $typeRule;

    /**
     * @before
     */
    public function setUpUser()
    {
        $this->user = new Child(['user_id' => 'english_student']);
    }

    /**
     * @before
     */
    public function setUpContainer()
    {
        $child = new Child(['user_id' => 'math_student']);
        $adult = new Adult(['user_id' => 'english_teacher']);
        $this->container = new SuggestionCollection();
        $this->container->append($child);
        $this->container->append($adult);
    }

    /**
     * @before
     */
    public function setUpTypeRule()
    {
        $this->typeRule = new TypeRule();
    }

    /**
     * @test
     */
    public function testItShouldRemoveSuggestionsOfDifferentType()
    {
        $this->typeRule->apply($this->container, $this->user);
        $this->assertFalse($this->container->offsetExists('english_teacher'));
    }
}
