<?php

namespace Skribble\Rule;

/**
 * Trait StateRuleTrait
 *
 * @todo Get position from asset using top and left
 * @todo Check Overlap using position
 */
trait StateRuleTrait
{
    /**
     * @var float
     */
    protected $left = 0.0;

    /**
     * @var float
     */
    protected $top = 0.0;

    /**
     * @var float
     */
    protected $scale = 0.0;

    /**
     * @var float
     */
    protected $rotation = 0.0;

    /**
     * @var int
     */
    protected $layer = 0;

    /**
     * @var array
     */
    protected $corners = [];

    /**
     * @return float
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param float $left
     *
     * @return StateRuleTrait
     */
    public function setLeft($left)
    {
        $this->left = $left;
        return $this;
    }

    /**
     * @return float
     */
    public function getTop()
    {
        return $this->top;
    }

    /**
     * @param float $top
     *
     * @return StateRuleTrait
     */
    public function setTop($top)
    {
        $this->top = $top;
        return $this;
    }

    /**
     * @return float
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param float $scale
     *
     * @return StateRuleTrait
     */
    public function setScale($scale)
    {
        $this->scale = $scale;
        return $this;
    }

    /**
     * @return float
     */
    public function getRotation()
    {
        return $this->rotation;
    }

    /**
     * @param float $rotation
     *
     * @return StateRuleTrait
     */
    public function setRotation($rotation)
    {
        $this->rotation = $rotation;
        return $this;
    }

    /**
     * @return int
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * @param int $layer
     *
     * @return StateRuleTrait
     */
    public function setLayer($layer)
    {
        $this->layer = $layer;
        return $this;
    }

    /**
     * @return array
     */
    public function getCorners()
    {
        return $this->corners;
    }

    /**
     * @param array $corners
     *
     * @return StateRuleTrait
     */
    public function setCorners(array $corners)
    {
        foreach ($corners as $corner) {
            // TODO confirm keys exist
            $this->addCorner($corners['x'], $corner['y']);
        }

        return $this;
    }

    /**
     * @param $cornerX
     * @param $cornerY
     */
    public function addCorner($cornerX, $cornerY)
    {
        array_push($this->corners, ['x' => $cornerX, 'y' => $cornerY]);
    }

    /**
     * @return array
     */
    public function getState()
    {
        return [
            'left'     => $this->getLeft(),
            'top'      => $this->getTop(),
            'scale'    => $this->getScale(),
            'rotation' => $this->getRotation(),
            'layer'    => $this->getLayer(),
            'valid'    => $this->isStateValid(),
            'corners'  => $this->getCorners(),
        ];
    }

    /**
     * @param array $state
     */
    public function setState(array $state)
    {
        $defaults = [
            'left'     => 0.0,
            'top'      => 0.0,
            'scale'    => 0.0,
            'rotation' => 0.0,
            'layer'    => 0,
            'corners'  => [],
        ];

        $array = array_merge($defaults, $state);

        $this->setLeft($array['left']);
        $this->setTop($array['top']);
        $this->setScale($array['scale']);
        $this->setRotation($array['rotation']);
        $this->setLayer($array['layer']);
        $this->setCorners($array['corners']);
    }

    /**
     * @return bool
     */
    public function isStateValid()
    {
        // TODO really validate state
        return true;
    }
}
