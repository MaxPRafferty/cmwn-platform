<?php

namespace app\Transformer;

use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    const MEMBER = 1;
    const MODERATOR = 2;
    const CONTROLLER = 3;

    protected $availableEmbeds = [];

    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */

    public function transform($role)
    {

        if ($role->entity == 'app\District' && $role->role_id >= self::MODERATOR) {
            return 'District Admin';
        }

        if ($role->entity == 'app\Organization' && $role->role_id >= self::MODERATOR) {
            return 'Principal';
        }

        if ($role->entity == 'app\Group' && $role->role_id >= self::MODERATOR) {
            return 'Teacher';
        }

        if ($role->entity == 'app\Group' && $role->role_id == self::MEMBER) {
            return 'Student';
        }

        return 'Role Unknown';

    }
}
