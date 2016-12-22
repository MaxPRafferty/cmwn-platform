# Rules Module

The rules engine provides a simple interface for adding business logic into the event manager.  Rules follow
a simple flow: When something happens -> If conditions are met -> Do something.  To a rule is defined using a 
specification.  A Specification has the following keys:

```php
$specification = new ArraySpecification([
    'id'      => 'foo-bar',
    'name'    => 'This is a test that the foo will bar',
    'when'    => 'some.event',
    'rules'   => [
        [
            'rule' => ['name' => AlwaysSatisfiedRule::class],
        ],
    ],
    'actions' => [
        new CallbackAction(function () {
            // I should do something
        }),
    ],
    'providers' => [
        'foo' => 'bar'
    ],
]);
```

Option        | Description
------------- | -----------
`id`          | A unique identifier for a rule
`name`        | The description for this rule
`when`        | The event to check for this rule
`rules`       | A Collection of rules to check against
`actions`     | Actions to take if the rules are satisfied
`providers`   | A Data provider that is passed into the rules and actions

## Rules

Rules MUST implement the ```Rules\RuleInterface``` which only requires ```isSatisfiedBy(RuleItemInterface $item): bool``` 
and ```timesSatisfied(): int``` to be defined.  A Rule Item is just a wrapper to the provider.  Rules can be be created
by the ServiceManager or invoked at runtime 

There are 5 basic rules:

- `Rule\Basic\AlwaysSatisfiedRule`: a rule that will always be satisfied
- `Rule\Basic\NeverSatisfiedRule`: a rule that is never satisfied
- `Rule\Basic\AndRule`: A rule that is satisfied when 1 or more rules are satisfied
- `Rule\Basic\EitherRule`: A rule that is satisfied when at least 1 rule is satisfied
- `Rule\Basic\NotRule`: This is satisfied when passed in rule is not satisfied 

### Date Rules

Date rules compare the current date to a specified date.  Time zoned are normalized to UTC.  There are 3 Date rules:

- `Rule\Date\DateAfter`: the current date must be after the specified date 
- `Rule\Date\DateBefore`: the current date must be before the specified date
- `Rule\Date\DateBetween`: current date must be between certain dates

### RuleCollection

Rules are stored in a ```RuleCollection```.  the rule collection will (by default) be satisfied when all rules are 
satisfied.   You can create groups of either rules by using the ```StaticRuleFactory```:

Example:

```php
$rules = StaticRuleFactory::build([
    [
        'rule'     => [
            'name' => NeverSatisfiedRule::class,
        ],
        'operator' => 'not',
    ],
    [
        'rule'     => [
            'name' => NeverSatisfiedRule::class,
        ],
        'operator' => 'or',
        'or_group' => 'foo-bar',
    ],
    [
        'rule'     => [
            'name' => AlwaysSatisfiedRule::class,
        ],
        'operator' => 'or',
        'or_group' => 'foo-bar',
    ]
]);
```

Since we define or groups, this example will be satisifed

## Actions

Actions MUST implement ```Rule\Action\ActionInterface``` which requires the ```__invoke(RuleItemInterface $item);```.  
Actions will only be invoked if the rules pass.  Actions can either come from the service manager or invoked at runtime

There are 2 default actions:

- `Rule\Action\CallbackAction`: Used to run a callable function
- `Rule\Action\NoopACtion`: A Lazy action that does nothing

### Action Collection

This is a holder for all actions for the EnginedSpecification.  Actions will be run in order in which they are appended

## Providers

Providers allows rules and actions to get access to data that is available outside the rules scope.  Data is stored as 
a key / value store.  A Provider can come from the ServiceManager or invoked at runtime.  There is currently only one 
type of provider:

- `Rule\Provider\BasicValueProvider`: a default provider that passed back was is passed in during construction 

### Provider Collection

This is a holder for all providers that allows access to all the data.

# TODO

- [ ] Add Provider Plugin manager
- [ ] Add Rule Plugin manager
- [ ] Add Action plugin manager
- [ ] Add DB Engine Specification


