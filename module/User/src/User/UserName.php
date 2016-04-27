<?php

namespace User;

/**
 * UserName Value object
 *
 * @package User
 * @property-read string $left
 * @property-read string $right
 * @property-read string $userName
 */
class UserName
{
    const SEPARATOR = '-';

    /**
     * @var string
     */
    protected $left;

    /**
     * @var string
     */
    protected $right;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @param string $left
     * @param string $right
     */
    public function __construct($left, $right)
    {
        $this->left     = $left;
        $this->right    = $right;
        $this->userName = $left . static::SEPARATOR . $right;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->userName;
    }

    /**
     * @param $name
     */
    public function __get($name)
    {
        if (!in_array($name, ['left', 'right', 'userName'])) {
            throw new \InvalidArgumentException('Invalid Property: ' . $name);
        }

        return $this->{$name};
    }

    /**
     * @param int $leftValue
     * @param int $rightValue
     */
    public function setValues($leftValue, $rightValue)
    {
        $leftValue     = abs($leftValue) < 1 ? 1 : $leftValue;
        $rightValue    = abs($rightValue) < 1 ? 1 : $rightValue;
        $userNumber    = $leftValue + $rightValue;
        $userNameCount = $userNumber < 1000 ? sprintf('%1$03d', ($leftValue + $rightValue)) : $userNumber;

        $this->userName = $this->left . static::SEPARATOR . $this->right . $userNameCount;
    }
}
