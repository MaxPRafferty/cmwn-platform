<?php

namespace Skribble\Rule;

use Zend\Filter\StaticFilter;

/**
 * Class SkribbleRules
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
        RuleCompositeInterface::TYPE_SOUND,
        RuleCompositeInterface::TYPE_BACKGROUND,
        RuleCompositeInterface::TYPE_EFFECT,
    ];

    /**
     * SkribbleRules constructor.
     */
    public function __construct()
    {
        $this->rules['items']    = new RuleCollection('items');
        $this->rules['messages'] = new RuleCollection('messages');
    }

    /**
     * @param Background $background
     */
    public function setBackground(Background $background)
    {
        $this->addRule($background);
    }

    /**
     * @param Sound $sound
     */
    public function setSound(Sound $sound)
    {
        $this->addRule($sound);
    }

    /**
     * @param Effect $effect
     */
    public function setEffect(Effect $effect)
    {
        $this->addRule($effect);
    }

    /**
     * @param array $items
     */
    public function setItems(array $items)
    {
        array_walk($items, [$this, 'addItem']);
    }

    /**
     * @param Item $item
     */
    public function addItem(Item $item)
    {
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
     * @param Message $message
     */
    public function addMessage(Message $message)
    {
        $this->addRule($message);
    }

    /**
     * @param RuleCompositeInterface $rule
     *
     * @return bool
     */
    public function addRule(RuleCompositeInterface $rule)
    {
        if (in_array($rule->getType(), $this->restricted) && null !== $this->rules[$rule->getType()]) {
            throw new \OverflowException(
                sprintf('Only one rule of type "%s" can be set', $rule->getType())
            );
        }

        $type = in_array($rule->getType(), ['item', 'message']) ? $rule->getType() . 's' : $rule->getType();

        if (!array_key_exists($type, $this->rules)) {
            throw new \UnexpectedValueException(
                sprintf('Rule of type %s is currently not supported', $rule->getType())
            );
        }

        $this->valid = $this->valid && $rule->isValid();
        if (in_array($rule->getType(), ['item', 'message'])) {
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
        $defaults = array_flip(array_keys($this->rules));

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
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
        // TODO: Implement getArrayCopy() method.
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
    public function getType()
    {
        return 'rules';
    }
}
