<?php

namespace Skribble\Rule;

/**
 * Interface StateAwareInterface
 */
interface StateRuleInterface
{
    /**
     * @param float $left
     *
     * @return StateRuleInterface
     */
    public function setLeft($left);

    /**
     * @return float
     */
    public function getTop();

    /**
     * @param float $top
     *
     * @return StateRuleInterface
     */
    public function setTop($top);

    /**
     * @return float
     */
    public function getScale();

    /**
     * @param float $scale
     *
     * @return StateRuleInterface
     */
    public function setScale($scale);

    /**
     * @return float
     */
    public function getRotation();

    /**
     * @param float $rotation
     *
     * @return StateRuleInterface
     */
    public function setRotation($rotation);

    /**
     * @return int
     */
    public function getLayer();

    /**
     * @param int $layer
     *
     * @return StateRuleInterface
     */
    public function setLayer($layer);

    /**
     * @return array
     */
    public function getCorners();

    /**
     * @param array $corners
     *
     * @return StateRuleInterface
     */
    public function setCorners(array $corners);

    /**
     * @return array
     */
    public function getState();

    /**
     * @param array $state
     *
     * @return StateRuleInterface
     */
    public function setState(array $state);

    /**
     * @param $cornerX
     * @param $cornerY
     */
    public function addCorner($cornerX, $cornerY);

    /**
     * @return bool
     */
    public function isStateValid();
}
