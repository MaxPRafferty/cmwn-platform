<?php

namespace Suggest\Rule;

use Suggest\InvalidArgumentException;
use Suggest\InvalidRuleException;
use Suggest\SuggestionCollection;
use User\UserInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RuleCollection
 * @package Suggest\Rule
 */
class RuleCollection implements RuleCompositeInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $service;

    /**
     * @var array List of rules to be applied
     */
    protected $rulesConfig = [];

    /**
     * @var RuleCompositeInterface[]
     */
    protected static $rules = [];

    /**
     * RuleCollection constructor.
     *
     * @param ServiceLocatorInterface $service
     * @param array $rulesConfig
     */
    public function __construct(ServiceLocatorInterface $service, array $rulesConfig)
    {
        $this->service = $service;
        $this->rulesConfig = $rulesConfig;
    }

    /**
     * Creates the rules from the service
     */
    protected function createRulesFromConfig()
    {
        if (!empty(self::$rules)) {
            return;
        }

        array_walk($this->rulesConfig, function ($ruleKey) {
            if (!$this->service->has($ruleKey)) {
                return;
            }

            $rule = $this->service->get($ruleKey);
            if (!$rule instanceof RuleCompositeInterface) {
                throw new InvalidRuleException();
            }

            $this->addRule($rule);
        });
    }

    /**
     * @param RuleCompositeInterface $rule
     */
    public function addRule(RuleCompositeInterface $rule)
    {
        array_push(self::$rules, $rule);
    }

    /**
     * @param SuggestionCollection $suggestionCollection
     * @param UserInterface $currentUser
     */
    public function apply(SuggestionCollection $suggestionCollection, UserInterface $currentUser)
    {
        $this->createRulesFromConfig();
        array_walk(
            self::$rules,
            function (RuleCompositeInterface $rule) use (&$suggestionCollection, &$currentUser) {
                $rule->apply($suggestionCollection, $currentUser);
            }
        );
    }
}
