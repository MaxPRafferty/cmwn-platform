<?php

namespace Skribble\Rule;

use Skribble\InvalidArgumentException;
use Skribble\OverflowException;
use Skribble\UnexpectedValueException;
use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

/**
 * Class RuleValidator
 */
class RuleValidator extends AbstractValidator implements ValidatorInterface
{
    const INVALID_TYPE = 'invalidType';
    const FATAL_ERROR  = 'unknown';
    const NOT_VALID    = 'notValid';

    /**
     * @var string[]
     */
    protected $messageTemplates = [
        self::INVALID_TYPE => 'Rules must be an array or object',
        self::FATAL_ERROR  => 'Unknown error occurred contact support', // TODO Better message
        self::NOT_VALID    => 'Some or all of the rules are invalid',
    ];

    /**
     * @inheritDoc
     */
    public function isValid($value)
    {
        // Skribble Rules does most of the checking itself
        if ($value instanceof SkribbleRules) {
            return true;
        }

        if (!is_array($value)) {
            $this->error(static::INVALID_TYPE);

            return false;
        }

        try {
            $rules = new SkribbleRules($value);
        } catch (InvalidArgumentException $invalidType) {
            $this->setMessage($invalidType->getMessage());
            return false;
        } catch (OverflowException $overFlow) {
            $this->setMessage($overFlow->getMessage());
            return false;
        } catch (UnexpectedValueException $unexpected) {
            $this->setMessage($unexpected->getMessage());
            return false;
        } catch (\Exception $unknown) {
            $this->error(static::FATAL_ERROR);
            return false;
        }

        if (!$rules->isValid()) {
            $this->error(static::NOT_VALID);
            return false;
        }

        return true;
    }
}
