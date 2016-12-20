<?php

namespace Suggest\Rule;

use Interop\Container\ContainerInterface;
use Suggest\InvalidRuleException;
use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Class RuleCollection
 * @package Suggest\Rule
 */
class RuleCollection implements RuleCompositeInterface
{
    /**
     * @var ContainerInterface
     */
    protected $service;

    /**
     * @var array List of rules to be applied
     */
    protected $rulesConfig = [];

    /**
     * @var RuleCompositeInterface[]
     */
    protected $rules = [];

    /**
     * RuleCollection constructor.
     *
     * @param ContainerInterface $service
     * @param array $rulesConfig
     */
    public function __construct(ContainerInterface $service, array $rulesConfig)
    {
        $this->service = $service;
        $this->rulesConfig = $rulesConfig;
    }

    /**
     * Creates the rules from the service
     */
    protected function createRulesFromConfig()
    {
        if (!empty($this->rules)) {
            return;
        }

        array_walk($this->rulesConfig, function ($ruleKey) {
            if (!$this->service->has($ruleKey)) {
                throw new InvalidRuleException(sprintf('Missing rule: "%s" from services', $ruleKey));
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
        array_push($this->rules, $rule);
    }

    /**
     * @param SuggestionCollection $suggestionCollection
     * @param UserInterface $currentUser
     */
    public function apply(SuggestionCollection $suggestionCollection, UserInterface $currentUser)
    {
        $this->createRulesFromConfig();
        array_walk(
            $this->rules,
            function (RuleCompositeInterface $rule) use (&$suggestionCollection, &$currentUser) {
                $rule->apply($suggestionCollection, $currentUser);
            }
        );
    }
}
