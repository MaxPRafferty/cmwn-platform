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
        AlwaysSatisfiedRule::class,
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
and ```timesSatisfied(): int``` to be defined.  A Rule Item is just a wrapper to the provider.   Rules can be created 
using the RulePluginManager.

There are 5 basic rules:

- `Rule\Basic\AlwaysSatisfiedRule`: a rule that will always be satisfied
- `Rule\Basic\NeverSatisfiedRule`: a rule that is never satisfied
- `Rule\Basic\AndRule`: A rule that is satisfied when 1 or more rules are satisfied
- `Rule\Basic\EitherRule`: A rule that is satisfied when at least 1 rule is satisfied
- `Rule\Basic\NotRule`: This is satisfied when passed in rule is not satisfied 

### Date Rules

Date rules compare the current date to a specified date.  Time zones are normalized to UTC.  There are 3 Date rules:

- `Rule\Date\DateAfter`: the current date must be after the specified date 
- `Rule\Date\DateBefore`: the current date must be before the specified date
- `Rule\Date\DateBetween`: current date must be between certain dates

### RuleCollection

Rules are stored in a ```RuleCollection```.  the rule collection will (by default) be satisfied when all rules are 
satisfied.  You can append rules to a collection using ```RuleCollection::append()```.  It takes three parameters:

- ```$rule``` - a ```RuleInterface``` 
- ```$operator``` - how the rule should be treated when trying to satisfy.  Must be one of three options:
    - ```and``` - All rules MUST BE satisfied
    - ```or``` - At least one rule MUST BE satisfied
    - ```not``` - Adds the rule as a ```NotRule```
- ```$orGroup``` - When the ```$operator``` is ```or``` Groups them together based on this group    

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

## Plugins

Rules, Actions, Specifications and Providers each have plugin managers that will handle building rules.  Each type has 
builder factories that allow easy creation.  

### ```Build*FromConfigFactory```

The ```Build*FromConfigFactory``` is an abstract factory that checks the config on how to build either a Specification, 
 Rule, Provider or Action.  Each factory requires a config that is marked with the class of the factory.  There is an 
optional key ```*_class``` which defines the class to use when building.  Each value is checked agains the ServiceManager
for a dependency needed from there.

For example:

```php
$config[BuildProviderFromConfigFactory::class] = [
   'foo-bar' => [
       'provider_class' => BasicValueProvider::class,
       'foo',
       'bar',
   ],

   'provider-with-dependency' => [
       'provider_class' => ProviderWithDependency::class,
       ProviderDependency::class,
   ],
];
```

```$providerManger->get('foo-bar')``` will return a ```BasicValueProvider``` that has ```foo``` for the provider name 
with a value of ```bar```

```$providerManger->get('provider-with-dependency')``` will return ```ProviderWithDependency``` with the dependency for 
```ProviderDependency``` from the ServiceManager

### ```Build*CollectionFactory``` 

These factories will build the respective collection objects.  Each Item will be built using the plugin manager related 
to the type of collection being built (```BuildProviderCollectionFactroy``` will use the ```ProviderManager```).  You 
can specify the collection class using the ```*_collection_class``` other wise it will default to the collection class

example:

```php
$collection = $providerManager->build(
    'MyCollection',
    [
        'provider_collection_class' => ProviderCollection::class,
        'providers'                 => [
            new BasicValueProvider('foo', 'bar'),
            'MyProvider',
            'provider' => [
                'name'    => BasicValueProvider::class,
                'options' => ['fizz', 'buzz'],
            ],
        ],
    ]
);
```

will build a Provider Collection that has three providers:
 
- a ```new BasicValueProvider('foo', 'bar')``` that was just passed in 
- a provider that came from the plugin manager 
- and a ```BasicValueProvider('foo', 'bar')``` that was built with the values fizz and buzz

### BuildDependentRuleFactory

This is a factory for the Rule Manager that is used to build rules that are dependent of other rules (like the basic rules)

Example:

```php
$ruleManager = $container->get(RuleManager::class);
$andRule = $ruleManager->build(
    EitherRule::class,
    [
        'rule_class' => EitherRule::class, // This is optional
        'rules'      => [
            new AlwaysSatisfiedRule(),
            AlwaysSatisfiedRule::class,
            [
                'name'    => NotRule::class,
                'options' => [new NeverSatisfiedRule()],
            ],
            [
                'name'     => NeverSatisfiedRule::class,
                'options'  => [],
                'operator' => 'or',
                'or_group' => 'foo-bar',
            ],
            [
                'name'     => AlwaysSatisfiedRule::class,
                'options'  => [],
                'operator' => 'or',
                'or_group' => 'foo-bar',
            ],
        ],
    ]
);
```

this will create an ```EitherRule``` that has **4** Rules that will always be satisfied: 
- Rule 1 is ```AlwaysSatisfiedRule```
- Rule 2 is ```AlwaysSatisfiedRule```
- Rule 3 is a ```NotRule``` with a ```NeverSatisfiedRule``` passed in 
- Rule 4 is an ```EitherRule``` with **2** rules a ```NeverSatisfiedRule``` and an ```AlwaysSatisfiedRule```

# TODO

- [ ] Add DB Engine Specification


