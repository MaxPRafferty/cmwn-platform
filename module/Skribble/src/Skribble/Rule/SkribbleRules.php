<?php

namespace Skribble\Rule;

use Skribble\InvalidArgumentException;
use Skribble\OverflowException;
use Skribble\UnexpectedValueException;
use Zend\Filter\StaticFilter;

/**
 * Class SkribbleRules
 * @todo Add ability to remove rules?
 */
class SkribbleRules implements RuleCompositeInterface, RuleSpecificationInterface
{
    /**
     * @var bool
     */
    protected $valid = true;

    /**
     * @var RuleInterface[]|RuleCollection[]
     */
    protected $rules = [
        'background' => null,
        'effect'     => null,
        'sound'      => null,
        'items'      => null,
        'messages'   => null,
    ];

    protected $restricted = [
        Sound::TYPE_SOUND,
        Background::TYPE_BACKGROUND,
        Effect::TYPE_EFFECT,
    ];

    /**
     * SkribbleRules constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->rules['items']    = new RuleCollection('items');
        $this->rules['messages'] = new RuleCollection('messages');
        $this->exchangeArray($options);
    }

    /**
     * @param Background|array $background
     */
    public function setBackground($background)
    {
        if (is_array($background)) {
            $background = RuleStaticFactory::createRuleFromArray($background);
        }

        if (!$background instanceof Background) {
            throw new InvalidArgumentException('Rule is not a Background');
        }

        $this->addRule($background);
    }

    /**
     * @param Sound|array $sound
     */
    public function setSound($sound)
    {
        if (is_array($sound)) {
            $sound = RuleStaticFactory::createRuleFromArray($sound);
        }

        if (!$sound instanceof Sound) {
            throw new InvalidArgumentException('Rule is not a Sound');
        }

        $this->addRule($sound);
    }

    /**
     * @param Effect|array $effect
     */
    public function setEffect($effect)
    {
        if (is_array($effect)) {
            $effect = RuleStaticFactory::createRuleFromArray($effect);
        }

        if (!$effect instanceof Effect) {
            throw new InvalidArgumentException('Rule is not an Effect');
        }

        $this->addRule($effect);
    }

    /**
     * @param array|Item[] $items
     */
    public function setItems(array $items)
    {
        array_walk($items, [$this, 'addItem']);
    }

    /**
     * @param Item|array $item
     */
    public function addItem($item)
    {
        if (is_array($item)) {
            $item = RuleStaticFactory::createRuleFromArray($item);
        }

        if (!$item instanceof Item) {
            throw new InvalidArgumentException('Rule is not an Item');
        }

        $this->addRule($item);
    }

    /**
     * @param array $messages
     */
    public function setMessages(array $messages)
    {
        array_walk($messages, [$this, 'addMessage']);
    }

    /**
     * @param Message|array $message
     */
    public function addMessage($message)
    {
        if (is_array($message)) {
            $message = RuleStaticFactory::createRuleFromArray($message);
        }

        if (!$message instanceof Message) {
            throw new InvalidArgumentException('Rule is not a Message');
        }

        $this->addRule($message);
    }

    /**
     * @param RuleCompositeInterface $rule
     *
     * @return bool
     */
    public function addRule(RuleCompositeInterface $rule)
    {
        if (in_array($rule->getRuleType(), $this->restricted) && null !== $this->rules[$rule->getRuleType()]) {
            throw new OverflowException(
                sprintf('Only one rule of type "%s" can be set', $rule->getRuleType())
            );
        }

        $type = in_array($rule->getRuleType(), ['item', 'message']) ? $rule->getRuleType() . 's' : $rule->getRuleType();

        if (!array_key_exists($type, $this->rules)) {
            throw new UnexpectedValueException(
                sprintf('Rule of type "%s" is currently not supported', $rule->getRuleType())
            );
        }

        $this->valid = $this->valid && $rule->isValid();
        if (in_array($rule->getRuleType(), ['item', 'message'])) {
            $this->rules[$type]->append($rule);

            return true;
        }

        $this->rules[$type] = $rule;

        return true;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'background' => null,
            'effect'     => null,
            'sound'      => null,
            'items'      => null,
            'messages'   => null,
        ];

        $array    = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            if (empty($value)) {
                continue;
            }

            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        $return = [];
        foreach ($this->rules as $spec => $rule) {
            $return[$spec] = null !== $rule ? $rule->getArrayCopy() : null;
        }

        return $return;
    }

    /**
     * Tests if this rule is valid or not
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * Gets the type of rule
     *
     * @return string
     */
    public function getRuleType()
    {
        return 'rules';
    }
}
