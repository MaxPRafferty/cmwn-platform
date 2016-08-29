<?php


namespace SuggestTest\Rule;

use Suggest\Rule\MeRule;
use Suggest\SuggestionContainer;
use User\Adult;
use User\Child;
use User\UserInterface;

/**
 * Class MeRuleTest
 * @package SuggestTest\Rule
 */
class MeRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SuggestionContainer
     */
    protected $container;

    /**
     * @var MeRule
     */
    protected $meRule;

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
        $this->container = new SuggestionContainer();
        $this->container[$this->user->getUserId()] = $this->user;
        $adult = new Adult(['user_id' => 'english_teacher']);
        $this->container[$adult->getUserId()] = $adult;
    }

    /**
     * @before
     */
    public function setUpMeRule()
    {
        $this->meRule = new MeRule();
    }

    /**
     * @test
     */
    public function testItShouldRemoveSuggestionIfMeUser()
    {
        $this->meRule->apply($this->container, $this->user);
        $expectedIds = ['english_teacher'];
        $actualIds = [];
        foreach ($this->container as $suggestion) {
            $actualIds[] = $suggestion->getUserId();
        }
        $this->assertEquals($expectedIds, $actualIds);
    }
}
