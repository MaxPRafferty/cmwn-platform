<?php
namespace Api\V1\Rest\Group;

use Group\Group;
use Group\GroupInterface;

/**
 * Class GroupEntity
 * @package Api\V1\Rest\Group
 */
class GroupEntity extends Group implements GroupInterface
{
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        unset($array['left']);
        unset($array['right']);
        unset($array['depth']);

        return $array;
    }
}
