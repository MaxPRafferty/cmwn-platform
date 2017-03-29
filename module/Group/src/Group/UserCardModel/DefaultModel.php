<?php

namespace Group\UserCardModel;

use Zend\View\Model\ViewModel;

/**
 * Class UserCardModel
 * @package Group\UserCardModel
 */
class DefaultModel extends ViewModel
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct();
        parent::setTemplate('usercard/default.phtml');
    }
}
