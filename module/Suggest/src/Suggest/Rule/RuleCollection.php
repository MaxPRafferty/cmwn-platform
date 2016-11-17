<?php

namespace Suggest\Rule;

use Suggest\InvalidArgumentException;
use Suggest\SuggestionContainer;
use User\UserInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RuleCollection
 * @package Suggest\Rule
 */
class RuleCollection implements SuggestedRuleCompositeInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @param ServiceLocatorInterface
     * @param array
     */
    public function __construct($service, $rules)
    {
        $this->service = $service;
        $this->createRulesFromConfig($rules);
    }

    /**
     * @param $rules
     */
    protected function createRulesFromConfig($rules)
    {
        foreach ($rules as $rule) {
            $rule = $this->service->get($rule);

            $this->addRule($rule);
        }
    }

    /**
     * @param $rule
     */
    public function addRule($rule)
    {
        if (!$rule instanceof SuggestedRuleCompositeInterface) {
            throw new InvalidArgumentException("Invalid Rule");
        }
        $this->rules[] = $rule;
    }

    /**
     * @param SuggestionContainer $suggestionContainer
     * @param UserInterface $currentUser
     */
    public function apply(SuggestionContainer $suggestionContainer, UserInterface $currentUser)
    {
        foreach ($this->rules as $rule) {
            $rule->apply($suggestionContainer, $currentUser);
        }
    }
}
