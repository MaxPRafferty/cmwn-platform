<?php

namespace User\Service;

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
     */
    public static function seedNames(array $nameList)
    {
        if (!array_key_exists('left', $nameList) || !array_key_exists('right', $nameList)) {
            throw new \InvalidArgumentException('Missing left or right values for names');
        }

        if (!is_array($nameList['left']) || !isset($nameList['right'])) {
            throw new \InvalidArgumentException('left or right values must be an array');
        }

        // The application will be calling this when bootstrapped
        // @codeCoverageIgnoreStart
        foreach ($nameList['left'] as $name) {
            array_push(static::$left, $name);
        }

        foreach ($nameList['right'] as $name) {
            array_push(static::$right, $name);
        }

        array_unique(static::$right);
        array_unique(static::$left);
        // @codeCoverageIgnoreEnd
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
     * @param \stdClass $generatedName
     * @return bool
     */
    public static function validateGeneratedName(\stdClass $generatedName)
    {
        if (!isset($generatedName->leftName)
            || !isset($generatedName->rightName)
            || !isset($generatedName->userName)) {
            return false;
        }

        $leftOk     = static::validateName($generatedName->leftName, static::POSITION_LEFT);
        $rightOk    = static::validateName($generatedName->rightName, static::POSITION_RIGHT);
        $userNameOk = $generatedName->leftName . '_' . $generatedName->rightName == $generatedName->userName;

        return $leftOk && $rightOk && $userNameOk;
    }

    /**
     * Generates a random name
     *
     * @return \stdClass
     */
    public static function generateRandomName()
    {
        $result = new \stdClass();

        $leftKey  = array_rand(static::$left, 1);
        $rightKey = array_rand(static::$right, 1);

        $result->leftName  = static::$left[$leftKey];
        $result->rightName = static::$right[$rightKey];
        $result->userName  = $result->leftName . '_' . $result->rightName;

        return $result;
    }
}
