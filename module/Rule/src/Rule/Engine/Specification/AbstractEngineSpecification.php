<?php

namespace Rule\Engine\Specification;

use Rule\Action\Collection\ActionCollectionAwareInterface;
use Rule\Action\Collection\ActionCollectionAwareTrait;
use Rule\Action\Collection\ActionCollectionInterface;
use Rule\Action\Service\ActionManager;
use Rule\Action\StaticActionFactory;
use Rule\Exception\RuntimeException;
use Rule\Provider\Collection\ProviderCollectionAwareInterface;
use Rule\Provider\Collection\ProviderCollectionAwareTrait;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Rule\Provider\Service\ProviderManager;
use Rule\Provider\StaticProviderCollectionFactory;
use Rule\Rule\Collection\RuleCollection;
use Rule\Rule\Collection\RuleCollectionAwareInterface;
use Rule\Rule\Collection\RuleCollectionAwareTrait;
use Rule\Rule\Collection\RuleCollectionInterface;
use Rule\Rule\Service\RuleManager;

/**
 * Class AbstractEngineSpecification
 */
abstract class AbstractEngineSpecification implements
    SpecificationInterface,
    ProviderCollectionAwareInterface,
    ActionCollectionAwareInterface,
    RuleCollectionAwareInterface
{
    use ProviderCollectionAwareTrait;
    use ActionCollectionAwareTrait;
    use RuleCollectionAwareTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $when = [];

    /**
     * @var array
     */
    protected $ruleSpec = [];

    /**
     * @var array
     */
    protected $actionSpec = [];

    /**
     * @var array
     */
    protected $providerSpec = [];

    /**
     * AbstractEngineSpecification constructor.
     *
     * @param string $id
     * @param string $name
     * @param array | string $when
     * @param array $rules
     * @param array $actions
     * @param array $providers
     */
    public function __construct(
        string $id,
        string $name,
        $when,
        array $rules,
        array $actions,
        array $providers = []
    ) {
        $this->id           = $id;
        $this->name         = $name;
        if (!is_string($when) && !is_array($when)) {
            throw new RuntimeException(sprintf(
                'The key "when" passed in specification %s should be an array or a string, %s given',
                $id,
                gettype($when)
            ));
        }
        $this->when         = is_string($when) ? [$when] : $when;
        $this->ruleSpec     = $rules;
        $this->actionSpec   = $actions;
        $this->providerSpec = $providers;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getEventName(): array
    {
        return $this->when;
    }

    /**
     * @inheritDoc
     */
    public function getRules(RuleManager $ruleManager): RuleCollectionInterface
    {
        if (null == $this->ruleCollection) {
            $this->setRulesCollection(
                $ruleManager->build(
                    RuleCollection::class,
                    $this->ruleSpec
                )
            );
        }

        return $this->getRulesCollection();
    }

    /**
     * @inheritDoc
     */
    public function getActions(ActionManager $actionManager): ActionCollectionInterface
    {
        if (null == $this->actionCollection) {
            $this->setActionCollection(
                $actionManager->build(
                    ActionCollectionInterface::class,
                    $this->actionSpec
                )
            );
        }

        return $this->getActionCollection();
    }

    /**
     * @inheritDoc
     */
    public function buildProvider(ProviderManager $providerManager): ProviderCollectionInterface
    {
        if (null === $this->providerCollection) {
            $this->setProviderCollection(
                $providerManager->build(
                    ProviderCollectionInterface::class,
                    $this->providerSpec ?? []
                )
            );
        }

        return $this->getProviderCollection();
    }
}
