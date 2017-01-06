<?php

namespace Application\Session;

use Zend\Session\Container;
use Zend\Session\ManagerInterface;

/**
 * A Session container for change my world now data
 */
class CmwnContainer extends Container
{
    /**
     * @inheritDoc
     */
    public function __construct(ManagerInterface $manager = null)
    {
        parent::__construct('cmwn', $manager);
    }
}
