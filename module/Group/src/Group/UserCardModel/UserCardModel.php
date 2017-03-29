<?php

namespace Group\UserCardModel;

use Zend\View\Model\ViewModel;

/**
 * Class UserCardModel
 * @package Group\UserCardModel
 */
class UserCardModel extends ViewModel
{
    /**
     * @inheritdoc
     */
    public function __construct($variables, $options = [])
    {
        parent::__construct($variables, $options);
        parent::setTemplate('usercard/user.cards.phtml');
    }
}
