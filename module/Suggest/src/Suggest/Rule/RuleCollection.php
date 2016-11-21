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
    protected static $rules;

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
        if (null !== self::$rules) {
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

            $this->addRule($ruleKey);
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
     * @param SuggestionCollection $suggestionContainer
     * @param UserInterface $currentUser
     */
    public function apply(SuggestionCollection $suggestionContainer, UserInterface $currentUser)
    {
        $this->createRulesFromConfig();
        array_walk(
            self::$rules,
            function (RuleCompositeInterface $rule) use (&$suggestionContainer, &$user) {
                $rule->apply($suggestionContainer, $user);
            }
        );
    }
}
