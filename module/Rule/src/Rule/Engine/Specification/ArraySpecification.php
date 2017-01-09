<?php

namespace Rule\Engine\Specification;

use Rule\Action\StaticActionFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\Exception\RuntimeException;
use Rule\Provider\StaticProviderCollectionFactory;

/**
 * An Engine Specification that is built from an array
 */
class ArraySpecification extends AbstractEngineSpecification implements SpecificationInterface
{
    /**
     * Used to specify engine rules from an array
     *
     * @param array $spec
     */
    public function __construct(array $spec)
    {
        // Check that we have all the required key
        foreach (['id', 'name', 'when', 'rules', 'actions'] as $required) {
            if (!isset($spec[$required]) || empty($spec[$required])) {
                throw new RuntimeException(sprintf(
                    'Missing required key "%s" for "%s"',
                    $required,
                    static::class
                ));
            }
        }

        // check that items are arrays
        foreach (['rules', 'actions', 'providers'] as $mustBeArray) {
            if (isset($spec[$mustBeArray]) && !is_array($spec[$mustBeArray])) {
                throw new InvalidArgumentException(sprintf(
                    'Key "%s" myst be an array for "%s"',
                    $mustBeArray,
                    static::class
                ));
            }
        }

        parent::__construct(
            $spec['id'],
            $spec['name'],
            $spec['when'],
            $spec['rules'],
            $spec['actions'],
            $spec['providers'] ?? []
        );
    }
}
