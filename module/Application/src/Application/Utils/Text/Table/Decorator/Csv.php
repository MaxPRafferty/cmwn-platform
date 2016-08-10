<?php

namespace Application\Utils\Text\Table\Decorator;

use Zend\Text\Table\Decorator\DecoratorInterface as Decorator;

/**
 * Class Csv
 *
 * This is the exact port of the decorator that is currently in PR for zendframework/zend-text
 */
class Csv implements Decorator
{
    /**
     * @var string
     */
    protected $separator = ',';

    /**
     * Csv constructor.
     *
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->separator = $separator;
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getTopLeft()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getTopRight()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getBottomLeft()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getBottomRight()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getVertical()
    {
        return $this->separator;
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getHorizontal()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getCross()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getVerticalRight()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getVerticalLeft()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getHorizontalDown()
    {
        return '';
    }

    /**
     * Defined by Zend\Text\Table\Decorator\DecoratorInterface
     *
     * @return string
     */
    public function getHorizontalUp()
    {
        return '';
    }
}
