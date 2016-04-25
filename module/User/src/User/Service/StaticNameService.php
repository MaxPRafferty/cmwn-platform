<?php

namespace User\Service;

use User\UserName;
use Zend\Math\Rand;

/**
 * Class StaticNameService
 * @package User\Service
 */
class StaticNameService
{
    const POSITION_LEFT  = 'LEFT';
    const POSITION_RIGHT = 'RIGHT';

    /**
     * @var array list of left side names
     */
    protected static $left  = [];

    /**
     * @var array list of right side names
     */
    protected static $right = [];

    /**
     * Seeds the list of names
     *
     * @param array $nameList
     * @codeCoverageIgnore
     */
    public static function seedNames(array $nameList)
    {
        if (!array_key_exists('left', $nameList) || !array_key_exists('right', $nameList)) {
            throw new \InvalidArgumentException('Missing left or right values for names');
        }

        if (!is_array($nameList['left']) || !isset($nameList['right'])) {
            throw new \InvalidArgumentException('left or right values must be an array');
        }

        foreach ($nameList['left'] as $name) {
            array_push(static::$left, $name);
        }

        foreach ($nameList['right'] as $name) {
            array_push(static::$right, $name);
        }

        array_unique(static::$right);
        array_unique(static::$left);
    }

    /**
     * Gets the list of names for a side
     *
     * @param $pos
     * @return array
     */
    public static function getNames($pos)
    {
        switch ($pos) {
            case static::POSITION_RIGHT:
                return static::$right;
                break;

            case static::POSITION_LEFT:
                return static::$left;
                break;

            default:
                throw new \InvalidArgumentException('Invalid position: ' . $pos);
        }
    }

    /**
     * Validates the name is in the list based on position
     *
     * @param $name
     * @param $pos
     * @return bool
     */
    public static function validateName($name, $pos)
    {
        return array_search($name, static::getNames($pos)) !== false;
    }

    /**
     * Validates that a generated name is correct
     *
     * @param UserName $generatedName
     * @return bool
     */
    public static function validateGeneratedName(UserName $generatedName)
    {
        $leftOk     = static::validateName($generatedName->left, static::POSITION_LEFT);
        $rightOk    = static::validateName($generatedName->right, static::POSITION_RIGHT);
        $userNameOk = $generatedName->left . UserName::SEPARATOR . $generatedName->right == $generatedName->userName;

        return $leftOk && $rightOk && $userNameOk;
    }

    /**
     * Generates a random name
     *
     * @return UserName
     */
    public static function generateRandomName()
    {
        $leftKey  = Rand::getInteger(0, count(static::$left) - 1);
        $rightKey = Rand::getInteger(0, count(static::$left) - 1);

        return new UserName(static::$left[$leftKey], static::$right[$rightKey]);
    }
}
